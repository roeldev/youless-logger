// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

package logging

import (
	"github.com/roeldev/youless-client"
	"github.com/rs/zerolog"
)

func PhaseReadingResponseObjectMarshaler(r youless.PhaseReadingResponse) zerolog.LogObjectMarshaler {
	return &phaseReadingResponseObjectMarshaler{r}
}

type phaseReadingResponseObjectMarshaler struct {
	youless.PhaseReadingResponse
}

func (om *phaseReadingResponseObjectMarshaler) MarshalZerologObject(e *zerolog.Event) {
	e.Uint8("tariff", om.Tariff)
	e.Array("current", zerolog.Arr().Float64(om.Current1).Float64(om.Current2).Float64(om.Current3))
	e.Array("power", zerolog.Arr().Int64(om.Power1).Int64(om.Power2).Int64(om.Power3))
	e.Array("voltage", zerolog.Arr().Float64(om.Voltage1).Float64(om.Voltage2).Float64(om.Voltage3))
}
