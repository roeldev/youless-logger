// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

package server

import (
	"github.com/go-logr/zerologr"
	"github.com/go-pogo/buildinfo"
	"github.com/go-pogo/errors"
	"github.com/go-pogo/serv"
	"github.com/go-pogo/telemetry"
	"github.com/prometheus/client_golang/prometheus"
	"github.com/prometheus/client_golang/prometheus/promhttp"
	"github.com/rs/zerolog"
	"go.opentelemetry.io/otel"
	"go.opentelemetry.io/otel/attribute"
	semconv "go.opentelemetry.io/otel/semconv/v1.24.0"
	"net/http"
	"strings"
	"time"
)

type Option interface {
	apply(app *Server, conf Config) error
}

type optionFunc func(app *Server, conf Config) error

func (fn optionFunc) apply(app *Server, conf Config) error { return fn(app, conf) }

func WithBuildInfo(bld *buildinfo.BuildInfo) Option {
	return optionFunc(func(app *Server, _ Config) error {
		app.build = bld
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
	})
}

func With(fn func(app *Server) Option) Option {
	return optionFunc(func(app *Server, conf Config) error {
		opt := fn(app)
		if opt == nil {
			return nil
		}

		if err := opt.apply(app, conf); err != nil {
			return err
		}
		if rr, ok := opt.(serv.RoutesRegisterer); ok {
			rr.RegisterRoutes(app.router)
		}
		return nil
	})
}

func WithRoutesRegisterer(r serv.RoutesRegisterer) Option {
	return optionFunc(func(app *Server, _ Config) error {
		r.RegisterRoutes(app.router)
		return nil
	})
}

func WithTelemetry(tc telemetry.Config) Option {
	return optionFunc(func(app *Server, c Config) error {
		var err error
		app.telem, err = telemetryBuilder(app, tc).Build()
		return err
	})
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
	return optionFunc(func(app *Server, c Config) error {
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
	})
}

func telemetryBuilder(app *Server, tc telemetry.Config) *telemetry.Builder {
	if tc.ServiceName == "" {
		if strings.HasPrefix(app.name, "youless-") {
			tc.ServiceName = app.name
		} else {
			tc.ServiceName = "youless-" + app.name
		}
	}

	telem := telemetry.NewBuilder(tc).Global().WithDefaultExporter()

	if app.build != nil {
		telem.TracerProvider.WithAttributes(
			semconv.ServiceVersion(app.build.Version),
			attribute.String("vcs.revision", app.build.Revision),
			attribute.String("vcs.time", app.build.Time.Format(time.RFC3339)),
		)
	}

	zl := app.log.Level(zerolog.DebugLevel)
	otel.SetLogger(zerologr.New(&zl))
	otel.SetErrorHandler(otel.ErrorHandlerFunc(func(err error) {
		app.log.Err(err).Msg("otel error")
	}))
	return telem
}
