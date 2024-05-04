// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

package server

import (
	"github.com/go-pogo/serv"
	"github.com/rs/zerolog"
	"go.opentelemetry.io/contrib/instrumentation/net/http/otelhttp"
	"net/http"
)

var _ serv.Router = (*router)(nil)

type router struct {
	*serv.ServeMux
	log *zerolog.Logger
}

func newRouter(log *zerolog.Logger) *router {
	return &router{
		ServeMux: serv.NewServeMux(),
		log:      log,
	}
}

func (r *router) Handle(pattern string, handler http.Handler) {
	r.HandleRoute(serv.Route{
		Pattern: pattern,
		Handler: handler,
	})
}

func (r *router) HandleFunc(pattern string, handler func(http.ResponseWriter, *http.Request)) {
	r.HandleRoute(serv.Route{
		Pattern: pattern,
		Handler: http.HandlerFunc(handler),
	})
}

func (r *router) HandleRoute(route serv.Route) {
	if r.log != nil {
		r.log.Debug().
			Str("name", route.Name).
			Str("method", route.Method).
			Str("pattern", route.Pattern).
			Msg("register route")
	}

	route.Handler = otelhttp.WithRouteTag(route.Pattern, route.Handler)
	r.ServeMux.HandleRoute(route)
}
