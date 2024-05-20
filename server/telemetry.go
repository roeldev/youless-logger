// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

package server

import (
	"github.com/go-logr/zerologr"
	"github.com/go-pogo/healthcheck"
	"github.com/go-pogo/telemetry"
	"github.com/rs/zerolog"
	"go.opentelemetry.io/otel"
	"go.opentelemetry.io/otel/attribute"
	semconv "go.opentelemetry.io/otel/semconv/v1.24.0"
	"golang.org/x/net/context"
	"strings"
	"sync"
	"time"
)

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

	health := &otelHealthChecker{timeout: tc.ExporterOTLP.TimeoutDuration()}
	app.health.Register("otel.otlp_exporter", health)
	otel.SetErrorHandler(otel.ErrorHandlerFunc(func(err error) {
		app.log.Err(err).Msg("otel error")
		go health.markError()
	}))
	return telem
}

var _ healthcheck.HealthChecker = (*otelHealthChecker)(nil)

type otelHealthChecker struct {
	mut     sync.RWMutex
	count   uint8
	last    time.Time
	timeout time.Duration
}

func (o *otelHealthChecker) CheckHealth(_ context.Context) healthcheck.Status {
	o.mut.RLock()
	defer o.mut.RUnlock()

	if o.count > 3 {
		return healthcheck.StatusUnhealthy
	}
	if o.last.IsZero() {
		return healthcheck.StatusUnknown
	}
	return healthcheck.StatusHealthy
}

func (o *otelHealthChecker) markError() {
	o.mut.Lock()
	defer o.mut.Unlock()

	if time.Since(o.last) > o.timeout {
		o.count = 1
	} else {
		o.count++
	}
}
