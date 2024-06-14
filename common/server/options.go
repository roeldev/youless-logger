// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

package server

import (
	"context"
	"net/http"

	"github.com/go-pogo/buildinfo"
	"github.com/go-pogo/healthcheck"
	"github.com/go-pogo/serv"
)

type Option func(app *Server, conf Config) error

func WithBuildInfo(bld *buildinfo.BuildInfo) Option {
	if bld == nil {
		return nil
	}

	return func(srv *Server, _ Config) error {
		srv.router.HandleRoute(serv.Route{
			Name:    BuildInfoRoute,
			Method:  http.MethodGet,
			Pattern: buildinfo.PathPattern,
			Handler: buildinfo.HTTPHandler(bld),
		})

		return nil
	}
}

func WithHealthChecker(hc *healthcheck.Checker) Option {
	if hc == nil {
		return nil
	}

	return func(srv *Server, conf Config) error {
		srv.router.HandleRoute(serv.Route{
			Name:    HealthCheckRoute,
			Method:  http.MethodGet,
			Pattern: healthcheck.PathPattern,
			Handler: healthcheck.HTTPHandler(hc),
		})

		hc.Register("youless.server", healthcheck.HealthCheckerFunc(func(_ context.Context) healthcheck.Status {
			switch srv.server.State() {
			case serv.StateUnstarted:
				return healthcheck.StatusUnknown
			case serv.StateStarted:
				return healthcheck.StatusHealthy
			default:
				return healthcheck.StatusUnhealthy
			}
		}))
		return nil
	}
}

func WithRoutesRegisterer(r serv.RoutesRegisterer) Option {
	return func(app *Server, _ Config) error {
		r.RegisterRoutes(app.router)
		return nil
	}
}

func WithNotFoundHandler(h http.Handler) Option {
	return func(app *Server, _ Config) error {
		app.router.WithNotFoundHandler(h)
		return nil
	}
}
