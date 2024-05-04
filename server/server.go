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

func New(name string, conf Config, log zerolog.Logger, opts ...Option) (*Server, error) {
	app := &Server{
		name:   name,
		log:    log,
		router: newRouter(&log),
	}

	logger := &logger{&app.log}
	err := app.health.With(
		healthcheck.WithLogger(logger),
	)
	if err != nil {
		return nil, err
	}

	for _, opt := range opts {
		if opt == nil {
			continue
		}

		err = errors.Append(err, opt.apply(app, conf))
		if rr, ok := opt.(serv.RoutesRegisterer); ok {
			rr.RegisterRoutes(app.router)
		}
	}
	if err != nil {
		return nil, err
	}

	if app.build != nil {
		app.router.HandleRoute(serv.Route{
			Name:    BuildInfoRoute,
			Method:  http.MethodGet,
			Pattern: buildinfo.Route,
			Handler: buildinfo.HttpHandler(app.build),
		})
	}
	app.router.HandleRoute(serv.Route{
		Name:    HealthCheckRoute,
		Method:  http.MethodGet,
		Pattern: healthcheck.PathPattern,
		Handler: healthcheck.HTTPHandler(&app.health),
	})
	app.router.HandleRoute(serv.Route{
		Name:    FaviconRoute,
		Method:  http.MethodGet,
		Pattern: "/favicon.ico",
		Handler: accesslog.IgnoreHandler(serv.NoContentHandler()),
	})

	if err = app.server.With(
		conf.Port,
		serv.DefaultConfig(),
		serv.WithName(app.name),
		serv.WithLogger(logger),
		serv.WithTLSConfig(easytls.DefaultTLSConfig(), conf.TLS),
	); err != nil {
		return nil, err
	}

	var handler http.Handler = app.router
	if conf.AccessLog {
		handler = accesslog.Middleware(logger, handler)
	}
	if app.telem != nil {
		handler = otelhttp.NewHandler(handler, app.name,
			otelhttp.WithMessageEvents(otelhttp.ReadEvents, otelhttp.WriteEvents),
			otelhttp.WithTracerProvider(app.telem.TracerProvider()),
			otelhttp.WithMeterProvider(app.telem.MeterProvider()),
		)
	}

	app.server.Handler = handler
	return app, nil
}

//func NewGRPCServer() {
//
//}

func (app *Server) Name() string { return app.name }

func (app *Server) Router() serv.Router { return app.router }

func (app *Server) HealthChecker() *healthcheck.Checker { return &app.health }

func (app *Server) MeterProvider() telemetry.MeterProvider {
	return app.telem.MeterProvider()
}

func (app *Server) TracerProvider() telemetry.TracerProvider {
	return app.telem.TracerProvider()
}

func (app *Server) Run(ctx context.Context) error {
	app.server.BaseContext = serv.BaseContext(ctx)
	return app.server.Run()
}

func (app *Server) Shutdown(ctx context.Context) error {
	if app.telem == nil {
		return app.server.Shutdown(ctx)
	}

	if err := app.telem.ForceFlush(ctx); err != nil {
		return err
	}

	wg, ctx := errgroup.WithContext(ctx)
	wg.Go(func() error {
		return app.server.Shutdown(ctx)
	})
	wg.Go(func() error {
		return app.telem.Shutdown(ctx)
	})
	return wg.Wait()
}
