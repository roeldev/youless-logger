// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

// Package server provides a generic server implementation which is used by the
// different applications to serve their endpoints.
package server

import (
	"context"
	"net/http"

	"github.com/go-pogo/easytls"
	"github.com/go-pogo/errors"
	"github.com/go-pogo/serv"
	"github.com/go-pogo/serv/accesslog"
	"github.com/go-pogo/telemetry"
	"go.opentelemetry.io/contrib/instrumentation/net/http/otelhttp"
)

type Logger interface {
	RegisterRouteLogger
	serv.Logger
	accesslog.Logger
}

type Config struct {
	Port      serv.Port `default:"2512"`
	AccessLog bool      `default:"true"`
	TLS       easytls.Config
}

func (c Config) Validate() error {
	return nil
}

type Server struct {
	router *router
	server serv.Server
}

func New(name string, conf Config, log Logger, telem *telemetry.Telemetry, opts ...Option) (*Server, error) {
	srv := &Server{
		router: newRouter(log),
	}

	// apply options
	var err error
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
		Name:    FaviconRoute,
		Method:  http.MethodGet,
		Pattern: "/favicon.ico",
		Handler: accesslog.IgnoreHandler(serv.NoContentHandler()),
	})

	// setup server
	if err = srv.server.With(
		conf.Port,
		serv.DefaultConfig(),
		serv.WithName(name),
		serv.WithLogger(log),
		serv.WithTLSConfig(easytls.DefaultTLSConfig(), conf.TLS),
	); err != nil {
		return nil, err
	}

	var handler http.Handler = srv.router
	if conf.AccessLog {
		handler = accesslog.Middleware(log, handler)
	}
	if telem != nil {
		handler = otelhttp.NewHandler(handler, name,
			otelhttp.WithMessageEvents(otelhttp.ReadEvents, otelhttp.WriteEvents),
			otelhttp.WithTracerProvider(telem.TracerProvider()),
			otelhttp.WithMeterProvider(telem.MeterProvider()),
		)
	}

	srv.server.Handler = handler
	return srv, nil
}

//func NewGRPCServer() {
//
//}

func (srv *Server) Name() string { return srv.server.Name() }

func (srv *Server) RouteHandler() serv.RouteHandler { return srv.router }

func (srv *Server) Run(ctx context.Context) error {
	srv.server.BaseContext = serv.BaseContext(ctx)
	return srv.server.Run()
}

func (srv *Server) Shutdown(ctx context.Context) error {
	return srv.server.Shutdown(ctx)
}
