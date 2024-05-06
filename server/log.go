// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

package server

import (
	"github.com/go-pogo/healthcheck"
	"github.com/go-pogo/serv"
	"github.com/go-pogo/serv/accesslog"
	"github.com/rs/zerolog"
	"golang.org/x/net/context"
	"net/http"
)

var (
	_ serv.Logger        = (*logger)(nil)
	_ accesslog.Logger   = (*logger)(nil)
	_ healthcheck.Logger = (*logger)(nil)
)

type logger struct{ *zerolog.Logger }

func (l *logger) ServerStart(name, addr string) {
	l.Logger.Info().
		Str("name", name).
		Str("addr", addr).
		Msg("server starting")
}

func (l *logger) ServerShutdown(name string) {
	l.Logger.Info().
		Str("name", name).
		Msg("server shutting down")
}

func (l *logger) ServerClose(name string) {
	l.Logger.Info().
		Str("name", name).
		Msg("server closing")
}

func (l *logger) Log(_ context.Context, det accesslog.Details, req *http.Request) {
	var lvl zerolog.Level
	switch true {
	case det.HandlerName == HealthCheckRoute || det.HandlerName == PrometheusMetricsRoute:
		lvl = zerolog.DebugLevel
	case det.StatusCode >= 400:
		lvl = zerolog.WarnLevel
	default:
		lvl = zerolog.InfoLevel
	}

	l.Logger.WithLevel(lvl).
		Str("server", det.ServerName).
		Str("handler", det.HandlerName).
		Str("user_agent", det.UserAgent).
		Str("remote_addr", accesslog.RemoteAddr(req)).
		Str("method", req.Method).
		Str("request_uri", accesslog.RequestURI(req)).
		Int("status_code", det.StatusCode).
		Int64("request_count", det.RequestCount).
		Int64("bytes_written", det.BytesWritten).
		Dur("duration", det.Duration).
		Msg(accesslog.Message)
}

func (l *logger) HealthChanged(status, oldStatus healthcheck.Status) {
	l.Logger.Info().
		Stringer("status", status).
		Stringer("old_status", oldStatus).
		Msg("health status changed")
}

func (l *logger) HealthChecked(name string, stat healthcheck.Status) {
	l.Logger.Debug().
		Str("name", name).
		Stringer("status", stat).
		Msg("health status checked")
}
