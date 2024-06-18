// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

package logging

import (
	"io"
	"net/http"
	"os"
	"path"
	"time"

	"github.com/go-pogo/buildinfo"
	"github.com/go-pogo/healthcheck"
	"github.com/go-pogo/serv"
	"github.com/go-pogo/serv/accesslog"
	youlessclient "github.com/roeldev/youless-client"
	"github.com/roeldev/youless-logger/common/server"
	"github.com/rs/zerolog"
	"golang.org/x/net/context"
)

var (
	//_ serv.ErrorLogger   = (*Logger)(nil)
	_ server.Logger        = (*Logger)(nil)
	_ healthcheck.Logger   = (*Logger)(nil)
	_ youlessclient.Logger = (*Logger)(nil)
)

type Logger struct{ zerolog.Logger }

func NewProductionLogger(level zerolog.Level, withTimestamp bool) *Logger {
	return newLogger(os.Stdout, level, withTimestamp)
}

func NewDevelopmentLogger(level zerolog.Level, withTimestamp bool) *Logger {
	out := zerolog.NewConsoleWriter()
	out.TimeFormat = time.StampMilli
	return newLogger(out, level, withTimestamp)
}

func newLogger(out io.Writer, level zerolog.Level, withTimestamp bool) *Logger {
	log := zerolog.New(out).Level(level)
	if withTimestamp {
		log = log.With().Timestamp().Logger()
	}
	return &Logger{log}
}

func (l *Logger) LogBuildInfo(bld *buildinfo.BuildInfo, modules ...string) {
	event := l.Logger.Info().
		Str("go_version", bld.GoVersion()).
		Str("version", bld.Version()).
		Str("vcs_revision", bld.Revision()).
		Time("vcs_time", bld.Time())

	for _, name := range modules {
		if mod := bld.Module(name); mod.Version != "" {
			event.Str("module_"+path.Base(mod.Path), mod.Version)
		}
	}

	event.Msg("buildinfo")
}

func (l *Logger) LogRegisterRoute(route serv.Route) {
	l.Logger.Debug().
		Str("name", route.Name).
		Str("method", route.Method).
		Str("pattern", route.Pattern).
		Msg("register route")
}

// LogServerStart is part of the [serv.Logger] interface.
func (l *Logger) LogServerStart(name, addr string) {
	l.Logger.Info().
		Str("name", name).
		Str("addr", addr).
		Msg("server starting")
}

// LogServerStartTLS is part of the [serv.Logger] interface.
func (l *Logger) LogServerStartTLS(name, addr, certFile, keyFile string) {
	l.Logger.Info().
		Str("name", name).
		Str("addr", addr).
		Str("cert_file", certFile).
		Str("key_file", keyFile).
		Msg("server starting")
}

// LogServerShutdown is part of the [serv.Logger] interface.
func (l *Logger) LogServerShutdown(name string) {
	l.Logger.Info().
		Str("name", name).
		Msg("server shutting down")
}

// LogServerClose is part of the [serv.Logger] interface.
func (l *Logger) LogServerClose(name string) {
	l.Logger.Info().
		Str("name", name).
		Msg("server closing")
}

// LogAccess is part of the [accesslog.Logger] interface.
func (l *Logger) LogAccess(_ context.Context, det accesslog.Details, req *http.Request) {
	var lvl zerolog.Level
	switch true {
	case det.HandlerName == server.HealthCheckRoute:
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

// LogHealthChanged is part of the [healthcheck.Logger] interface.
func (l *Logger) LogHealthChanged(status, oldStatus healthcheck.Status, statuses map[string]healthcheck.Status) {
	l.Logger.Info().
		Stringer("status", status).
		Stringer("old_status", oldStatus).
		Msg("health changed")

	for name, stat := range statuses {
		l.Logger.Debug().
			Str("name", name).
			Stringer("status", stat).
			Msg("health")
	}
}

// LogClientRequest is part of the [youlessclient.Logger] interface.
func (l *Logger) LogClientRequest(_ context.Context, name, url string, shared bool) {
	l.Logger.Debug().
		Str("client", name).
		Str("url", url).
		Bool("shared", shared).
		Msg("client request")
}

// LogFetchAuthCookie is part of the [youlessclient.Logger] interface.
func (l *Logger) LogFetchAuthCookie(name string, cookie http.Cookie) {
	l.Logger.Info().
		Str("client", name).
		Str("cookie", cookie.String()).
		Msg("fetched auth cookie")
}
