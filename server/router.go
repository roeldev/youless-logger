// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

package server

import (
	"github.com/go-pogo/serv"
	"github.com/rs/zerolog"
	"github.com/uptrace/bunrouter"
	"go.opentelemetry.io/contrib/instrumentation/net/http/otelhttp"
	"net/http"
)

var _ serv.Router = (*router)(nil)

type router struct {
	log *zerolog.Logger
	mux *bunrouter.Router
}

func newRouter(log *zerolog.Logger, handler http.Handler, opts ...bunrouter.Option) *router {
	if handler != nil {
		opts = append(opts, bunrouter.WithNotFoundHandler(bunrouter.HTTPHandler(handler)))
	}

	return &router{
		log: log,
		mux: bunrouter.New(opts...),
	}
}

func (r *router) HandleRoute(route serv.Route) {
	if r.log != nil {
		r.log.Debug().
			Str("name", route.Name).
			Str("method", route.Method).
			Str("pattern", route.Pattern).
			Msg("register route")
	}

	r.mux.Handle(
		route.Method,
		route.Pattern,
		bunrouter.HTTPHandler(otelhttp.WithRouteTag(route.Pattern, route)),
	)
}

func (r *router) ServeHTTP(writer http.ResponseWriter, request *http.Request) {
	r.mux.ServeHTTP(writer, request)
}
