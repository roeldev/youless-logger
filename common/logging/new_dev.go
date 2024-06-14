// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

//go:build dev

package logging

import (
	"fmt"
	"os"

	"github.com/rs/zerolog"
)

func init() {
	zerolog.ErrorMarshalFunc = func(err error) interface{} {
		_, _ = fmt.Fprintf(os.Stdout, "\n%+v\n", err)
		return fmt.Sprintf("%v", err)
	}
}

func New(level zerolog.Level, withTimestamp bool) *Logger {
	return NewDevelopmentLogger(level, withTimestamp)
}
