// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

// Package server provides a generic server implementation which is used by the
// different applications to serve their build info, health status and API.
package server

import (
	"context"
	"github.com/go-pogo/buildinfo"
	"github.com/go-pogo/easytls"
	"github.com/go-pogo/errors"
	"github.com/go-pogo/errors/errgroup"
	"github.com/go-pogo/healthcheck"
	"github.com/go-pogo/serv"
	"github.com/go-pogo/serv/accesslog"
	"github.com/go-pogo/telemetry"
	youlessclient "github.com/roeldev/youless-client"
	"github.com/rs/zerolog"
	"go.opentelemetry.io/contrib/instrumentation/net/http/otelhttp"
	"net/http"
)

const (
	BuildInfoRoute         = buildinfo.MetricName
	HealthCheckRoute       = "healthcheck"
	FaviconRoute           = "favicon"
	PrometheusMetricsRoute = "prometheus-metrics"

	ErrInvalidPrometheusPath errors.Msg = "invalid prometheus path"
	ConfigValidationError               = youlessclient.ConfigValidationError
)

type Config struct {
	Port      serv.Port `default:"2512"`
	AccessLog bool      `default:"true"`
	TLS       easytls.Config
}

func (c Config) Validate() error {
	return nil
}

type Server struct {
	name   string
	build  *buildinfo.BuildInfo
	log    zerolog.Logger
	telem  *telemetry.Telemetry
	health healthcheck.Checker
	router *router
	server serv.Server
}

func New(name string, conf Config, zl zerolog.Logger, opts ...Option) (*Server, error) {
	srv := &Server{
		name:   name,
		log:    zl,
		router: newRouter(&zl),
	}
	log := &logger{&srv.log}

	// setup health checker
	err := srv.health.With(
		healthcheck.WithLogger(log),
	)
	if err != nil {
		return nil, err
	}

	// apply options
	for _, opt := range opts {
		if opt == nil {
			continue
		}

		err = errors.Append(err, opt(srv, conf))
	}
	if err != nil {
		return nil, err
	}

	// add common routes
	srv.router.HandleRoute(serv.Route{
		Name:    HealthCheckRoute,
		Method:  http.MethodGet,
		Pattern: healthcheck.PathPattern,
		Handler: healthcheck.HTTPHandler(&srv.health),
	})
	srv.router.HandleRoute(serv.Route{
		Name:    FaviconRoute,
		Method:  http.MethodGet,
		Pattern: "/favicon.ico",
		Handler: accesslog.IgnoreHandler(serv.NoContentHandler()),
	})

	// setup server
	if err = srv.server.With(
		conf.Port,
		serv.DefaultConfig(),
		serv.WithName(srv.name),
		serv.WithLogger(log),
		serv.WithTLSConfig(easytls.DefaultTLSConfig(), conf.TLS),
	); err != nil {
		return nil, err
	}

	var handler http.Handler = srv.router
	if conf.AccessLog {
		handler = accesslog.Middleware(log, handler)
	}
	if srv.telem != nil {
		handler = otelhttp.NewHandler(handler, srv.name,
			otelhttp.WithMessageEvents(otelhttp.ReadEvents, otelhttp.WriteEvents),
			otelhttp.WithTracerProvider(srv.telem.TracerProvider()),
			otelhttp.WithMeterProvider(srv.telem.MeterProvider()),
		)
	}

	srv.server.Handler = handler

	// enable server health checking
	srv.health.Register("server", healthcheck.HealthCheckerFunc(func(_ context.Context) healthcheck.Status {
		switch srv.server.State() {
		case serv.StateUnstarted:
			return healthcheck.StatusUnknown
		case serv.StateStarted:
			return healthcheck.StatusHealthy
		default:
			return healthcheck.StatusUnhealthy
		}
	}))

	return srv, nil
}

//func NewGRPCServer() {
//
//}

func (srv *Server) Name() string { return srv.name }

func (srv *Server) Router() serv.Router { return srv.router }

func (srv *Server) HealthChecker() *healthcheck.Checker { return &srv.health }

func (srv *Server) MeterProvider() telemetry.MeterProvider {
	return srv.telem.MeterProvider()
}

func (srv *Server) TracerProvider() telemetry.TracerProvider {
	return srv.telem.TracerProvider()
}

func (srv *Server) Run(ctx context.Context) error {
	srv.server.BaseContext = serv.BaseContext(ctx)
	return srv.server.Run()
}

func (srv *Server) Shutdown(ctx context.Context) error {
	if srv.telem == nil {
		return srv.server.Shutdown(ctx)
	}

	wg, ctx := errgroup.WithContext(ctx)
	wg.Go(func() error {
		return srv.server.Shutdown(ctx)
	})
	wg.Go(func() error {
		if err := srv.telem.ForceFlush(ctx); err != nil {
			return err
		}

		return srv.telem.Shutdown(ctx)
	})
	return wg.Wait()
}
