// Copyright (c) 2024, Roel Schut. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

package logging

import (
	"github.com/roeldev/youless-client"
	"github.com/rs/zerolog"
)

type meterReadingResponseObjectMarshaler struct {
	youless.MeterReadingResponse
}

// MeterReadingResponseObjectMarshaler wraps a youless.MeterReadingResponse so
// it becomes a zerolog.LogObjectMarshaler.
func MeterReadingResponseObjectMarshaler(r youless.MeterReadingResponse) zerolog.LogObjectMarshaler {
	return &meterReadingResponseObjectMarshaler{r}
}

func (m *meterReadingResponseObjectMarshaler) MarshalZerologObject(e *zerolog.Event) {
	if m.ElectricityReading.NetElectricity != 0 || m.ElectricityReading.Power != 0 {
		e.Object("electricity", ElectricityReadingObjectMarshaler(m.ElectricityReading))
	}
	if m.S0Reading.S0Timestamp != 0 {
		e.Object("s0", S0ReadingObjectMarshaler(m.S0Reading))
	}
	if m.GasReading.GasTimestamp != 0 {
		e.Object("gas", GasReadingObjectMarshaler(m.GasReading))
	}
	if m.WaterReading.WaterTimestamp != 0 {
		e.Object("water", WaterReadingObjectMarshaler(m.WaterReading))
	}
}

type electricityReadingObjectMarshaler struct {
	youless.ElectricityReading
}

// ElectricityReadingObjectMarshaler wraps a youless.ElectricityReading so it
// becomes a zerolog.LogObjectMarshaler.
func ElectricityReadingObjectMarshaler(r youless.ElectricityReading) zerolog.LogObjectMarshaler {
	return &electricityReadingObjectMarshaler{r}
}

func (om *electricityReadingObjectMarshaler) MarshalZerologObject(e *zerolog.Event) {
	e.Time("timestamp", om.Time())
	e.Array("import", zerolog.Arr().Float64(om.ElectricityImport1).Float64(om.ElectricityImport2))
	e.Array("export", zerolog.Arr().Float64(om.ElectricityExport1).Float64(om.ElectricityExport2))
	e.Float64("net", om.NetElectricity)
	e.Int64("power", om.Power)
}

type s0ReadingObjectMarshaler struct {
	youless.S0Reading
}

// S0ReadingObjectMarshaler wraps a youless.S0Reading so it becomes a
// zerolog.LogObjectMarshaler.
func S0ReadingObjectMarshaler(r youless.S0Reading) zerolog.LogObjectMarshaler {
	return &s0ReadingObjectMarshaler{r}
}

func (om *s0ReadingObjectMarshaler) MarshalZerologObject(e *zerolog.Event) {
	e.Time("timestamp", om.Time())
	e.Float64("total", om.S0Total)
	e.Int64("current", om.S0)
}

type gasReadingObjectMarshaler struct {
	youless.GasReading
}

// GasReadingObjectMarshaler wraps a youless.GasReading so it becomes a
// zerolog.LogObjectMarshaler.
func GasReadingObjectMarshaler(r youless.GasReading) zerolog.LogObjectMarshaler {
	return &gasReadingObjectMarshaler{r}
}

func (om *gasReadingObjectMarshaler) MarshalZerologObject(e *zerolog.Event) {
	timestamp(e, om.GasTimestamp)
	e.Float64("total", om.GasTotal)
}

type waterReadingObjectMarshaler struct {
	youless.WaterReading
}

// WaterReadingObjectMarshaler wraps a youless.WaterReading so it becomes a
// zerolog.LogObjectMarshaler.
func WaterReadingObjectMarshaler(r youless.WaterReading) zerolog.LogObjectMarshaler {
	return &waterReadingObjectMarshaler{r}
}

func (om *waterReadingObjectMarshaler) MarshalZerologObject(e *zerolog.Event) {
	timestamp(e, om.WaterTimestamp)
	e.Float64("total", om.WaterTotal)
}

func timestamp(e *zerolog.Event, ts uint64) {
	if t, err := youless.ParseTimestamp(ts); err != nil {
		e.Time("timestamp", t)
	} else {
		e.Uint64("timestamp", ts)
	}
}
