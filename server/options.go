// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

package server

import (
	"github.com/go-pogo/buildinfo"
	"github.com/go-pogo/errors"
	"github.com/go-pogo/healthcheck"
	"github.com/go-pogo/serv"
	"github.com/go-pogo/telemetry"
	"github.com/prometheus/client_golang/prometheus"
	"github.com/prometheus/client_golang/prometheus/promhttp"
	"net/http"
)

type Option func(app *Server, conf Config) error

func WithBuildInfo(bld *buildinfo.BuildInfo) Option {
	return func(app *Server, _ Config) error {
		app.build = bld
		app.router.HandleRoute(serv.Route{
			Name:    BuildInfoRoute,
			Method:  http.MethodGet,
			Pattern: buildinfo.Route,
			Handler: buildinfo.HttpHandler(app.build),
		})

		event := app.log.Info().
			Str("go_version", bld.GoVersion()).
			Str("version", bld.Version).
			Str("vcs_revision", bld.Revision).
			Time("vcs_time", bld.Time)

		for k, v := range bld.Extra {
			event = event.Str(k, v)
		}

		event.Msg("buildinfo")
		return nil
	}
}

//func With(fn func(app *Server) Option) Option {
//	return func(app *Server, conf Config) error {
//		opt := fn(app)
//		if opt == nil {
//			return nil
//		}
//
//		if err := opt(app, conf); err != nil {
//			return err
//		}
//		if rr, ok := opt.(serv.RoutesRegisterer); ok {
//			rr.RegisterRoutes(app.router)
//		}
//		return nil
//	}
//}

func WithRoutesRegisterer(r serv.RoutesRegisterer) Option {
	return func(app *Server, _ Config) error {
		r.RegisterRoutes(app.router)
		return nil
	}
}

func WithHealthChecker(name string, check healthcheck.HealthChecker) Option {
	return func(app *Server, _ Config) error {
		app.health.Register(name, check)
		return nil
	}
}

func WithNotFoundHandler(h http.Handler) Option {
	return func(app *Server, _ Config) error {
		app.router.WithNotFoundHandler(h)
		return nil
	}
}

func WithTelemetry(tc telemetry.Config) Option {
	return func(app *Server, c Config) error {
		var err error
		app.telem, err = telemetryBuilder(app, tc).Build()
		return err
	}
}

type PrometheusConfig struct {
	Enabled bool
	Path    string `default:"/metrics"`
}

func (c PrometheusConfig) Validate() error {
	if c.Enabled && (c.Path == "" || c.Path == "/") {
		return errors.WithKind(ErrInvalidPrometheusPath, ConfigValidationError)
	}
	return nil
}

func WithTelemetryAndPrometheus(tc telemetry.Config, pc PrometheusConfig) Option {
	return func(app *Server, c Config) error {
		telem := telemetryBuilder(app, tc)

		if pc.Enabled {
			prom := prometheus.NewRegistry()
			telem.MeterProvider.WithPrometheusExporter(prom)
			app.router.HandleRoute(serv.Route{
				Name:    PrometheusMetricsRoute,
				Method:  http.MethodGet,
				Pattern: pc.Path,
				Handler: promhttp.InstrumentMetricHandler(
					prom,
					promhttp.HandlerFor(prom, promhttp.HandlerOpts{}),
				),
			})
		}

		var err error
		app.telem, err = telem.Build()
		return err
	}
}
