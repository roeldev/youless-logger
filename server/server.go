// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

// Package server provides a generic server implementation which is used by the
// different applications to serve their build info, health status and API.
package server

import (
	"context"
	"github.com/go-pogo/buildinfo"
	"github.com/go-pogo/errors"
	"github.com/go-pogo/errors/errgroup"
	"github.com/go-pogo/serv"
	"github.com/go-pogo/serv/accesslog"
	"github.com/go-pogo/serv/middleware"
	"github.com/go-pogo/telemetry"
	"github.com/rs/zerolog"
	"go.opentelemetry.io/contrib/instrumentation/net/http/otelhttp"
	"net/http"
)

const (
	RouteBuildInfo   = buildinfo.MetricName
	RouteHealthCheck = "healthcheck"
	RouteMetrics     = "prometheus-metrics"
)

type Config struct {
	Port      serv.Port `default:"2512"`
	AccessLog bool      `default:"true"`
	TLS       serv.TLSConfig
}

type Server struct {
	name       string
	build      *buildinfo.BuildInfo
	log        zerolog.Logger
	telem      *telemetry.Telemetry
	router     serv.Router
	server     serv.Server
	middleware middleware.Middleware
}

func New(name string, conf Config, log zerolog.Logger, handler http.Handler, opts ...Option) (*Server, error) {
	app := &Server{
		name:   name,
		log:    log,
		router: newRouter(&log, handler),
	}

	var err error
	for _, opt := range opts {
		err = errors.Append(err, opt.apply(app, conf))
		if rr, ok := opt.(serv.RoutesRegisterer); ok {
			rr.RegisterRoutes(app.router)
		}
	}
	if err != nil {
		return nil, err
	}

	app.router.HandleRoute(serv.Route{
		Name:    RouteBuildInfo,
		Method:  http.MethodGet,
		Pattern: buildinfo.Route,
		Handler: buildinfo.HttpHandler(app.build),
	})
	app.router.HandleRoute(serv.Route{
		Name:    RouteHealthCheck,
		Method:  http.MethodGet,
		Pattern: "/healthy",
		Handler: http.HandlerFunc(func(wri http.ResponseWriter, req *http.Request) {
			_, _ = wri.Write([]byte("OK"))
		}),
	})
	app.router.HandleRoute(serv.Route{
		Name:    "favicon",
		Method:  http.MethodGet,
		Pattern: "/favicon.ico",
		Handler: http.HandlerFunc(func(wri http.ResponseWriter, req *http.Request) {
			accesslog.SetShouldIgnore(req.Context(), true)
			wri.WriteHeader(http.StatusNoContent)
		}),
	})

	serverLogger := &logger{&app.log}
	if conf.AccessLog {
		app.middleware = append(app.middleware, accesslog.Middleware(serverLogger))
	}
	if err = app.server.With(
		conf.Port,
		serv.DefaultConfig(),
		serv.WithName(app.name),
		serv.WithLogger(serverLogger),
	); err != nil {
		return nil, err
	}

	handler = app.middleware.Wrap(app.router.ServeHTTP)
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

func NewGRPCServer() {

}

func (app *Server) Name() string { return app.name }

func (app *Server) Router() serv.Router { return app.router }

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
