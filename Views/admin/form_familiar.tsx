'use client'

import { useState } from 'react'
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"

export default function FormularioFamiliar() {
  const [vinculo, setVinculo] = useState('')
  const [fallecio, setFallecio] = useState('NO')
  const [tieneHijos, setTieneHijos] = useState('NO')

  const renderCamposComunes = () => (
    <>
      <div className="grid grid-cols-2 gap-4">
        <div className="space-y-2">
          <Label htmlFor="apellido">Apellido</Label>
          <Input id="apellido" placeholder="Ingrese apellido" />
        </div>
        <div className="space-y-2">
          <Label htmlFor="nombre">Nombre</Label>
          <Input id="nombre" placeholder="Ingrese nombre" />
        </div>
      </div>
      <div className="space-y-2">
        <Label htmlFor="edad">Edad</Label>
        <Input id="edad" type="number" placeholder="Ingrese edad" />
      </div>
    </>
  )

  const renderCamposFallecimiento = () => (
    <>
      <div className="space-y-2">
        <Label htmlFor="fechaFallecimiento">Fecha de Fallecimiento</Label>
        <Input id="fechaFallecimiento" type="date" />
      </div>
      <div className="space-y-2">
        <Label htmlFor="causaFallecimiento">Causa de Fallecimiento</Label>
        <Input id="causaFallecimiento" placeholder="Ingrese causa de fallecimiento" />
      </div>
    </>
  )

  const renderCamposEspecificos = () => {
    switch (vinculo) {
      case 'PADRE':
      case 'MADRE':
        return (
          <>
            {renderCamposComunes()}
            <div className="space-y-2">
              <Label htmlFor="nacionalidad">Nacionalidad</Label>
              <Input id="nacionalidad" placeholder="Ingrese nacionalidad" />
            </div>
            <div className="space-y-2">
              <Label htmlFor="estadoCivil">Estado Civil</Label>
              <Select>
                <SelectTrigger>
                  <SelectValue placeholder="Seleccione estado civil" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="SOLTERO">Soltero/a</SelectItem>
                  <SelectItem value="CASADO">Casado/a</SelectItem>
                  <SelectItem value="VIUDO">Viudo/a</SelectItem>
                  <SelectItem value="DIVORCIADO">Divorciado/a</SelectItem>
                  <SelectItem value="SEPARADO">Separado/a de Hecho</SelectItem>
                  <SelectItem value="CONCUBINO">Concubino/a</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <RadioGroup defaultValue="NO" onValueChange={setFallecio}>
              <div className="flex items-center space-x-2">
                <Label>Falleció</Label>
                <RadioGroupItem value="SI" id="fallecio-si" />
                <Label htmlFor="fallecio-si">Sí</Label>
                <RadioGroupItem value="NO" id="fallecio-no" />
                <Label htmlFor="fallecio-no">No</Label>
              </div>
            </RadioGroup>
            {fallecio === 'SI' && renderCamposFallecimiento()}
            <div className="space-y-2">
              <Label htmlFor="gradoInstruccion">Grado de Instrucción</Label>
              <Input id="gradoInstruccion" placeholder="Ingrese grado de instrucción" />
            </div>
            <div className="space-y-2">
              <Label htmlFor="ocupacion">Ocupación</Label>
              <Input id="ocupacion" placeholder="Ingrese ocupación" />
            </div>
          </>
        )
      case 'HERMANO':
        return (
          <>
            {renderCamposComunes()}
          </>
        )
      case 'CONCUBINO':
      case 'ESPOSO':
        return (
          <>
            {renderCamposComunes()}
            <div className="space-y-2">
              <Label htmlFor="nacionalidad">Nacionalidad</Label>
              <Input id="nacionalidad" placeholder="Ingrese nacionalidad" />
            </div>
            <div className="space-y-2">
              <Label htmlFor="gradoInstruccion">Grado de Instrucción</Label>
              <Input id="gradoInstruccion" placeholder="Ingrese grado de instrucción" />
            </div>
            <div className="space-y-2">
              <Label htmlFor="ocupacion">Ocupación</Label>
              <Input id="ocupacion" placeholder="Ingrese ocupación" />
            </div>
            <RadioGroup defaultValue="NO" onValueChange={setTieneHijos}>
              <div className="flex items-center space-x-2">
                <Label>¿Tienen hijos?</Label>
                <RadioGroupItem value="SI" id="hijos-si" />
                <Label htmlFor="hijos-si">Sí</Label>
                <RadioGroupItem value="NO" id="hijos-no" />
                <Label htmlFor="hijos-no">No</Label>
              </div>
            </RadioGroup>
            {tieneHijos === 'SI' && (
              <div className="space-y-2">
                <Label htmlFor="cantidadHijos">Cantidad de hijos</Label>
                <Input id="cantidadHijos" type="number" placeholder="Ingrese cantidad de hijos" />
              </div>
            )}
            <RadioGroup defaultValue="NO" onValueChange={setFallecio}>
              <div className="flex items-center space-x-2">
                <Label>Falleció</Label>
                <RadioGroupItem value="SI" id="fallecio-si" />
                <Label htmlFor="fallecio-si">Sí</Label>
                <RadioGroupItem value="NO" id="fallecio-no" />
                <Label htmlFor="fallecio-no">No</Label>
              </div>
            </RadioGroup>
            {fallecio === 'SI' && renderCamposFallecimiento()}
          </>
        )
      case 'HIJO':
        return (
          <>
            {renderCamposComunes()}
          </>
        )
      default:
        return null
    }
  }

  return (
    <Card className="w-full max-w-2xl mx-auto">
      <CardHeader>
        <CardTitle>Formulario Familiar</CardTitle>
      </CardHeader>
      <CardContent className="space-y-4">
        <div className="space-y-2">
          <Label htmlFor="vinculo">Vínculo Familiar</Label>
          <Select onValueChange={setVinculo}>
            <SelectTrigger>
              <SelectValue placeholder="Seleccione vínculo familiar" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="PADRE">Padre</SelectItem>
              <SelectItem value="MADRE">Madre</SelectItem>
              <SelectItem value="HERMANO">Hermano/a</SelectItem>
              <SelectItem value="CONCUBINO">Concubino/a</SelectItem>
              <SelectItem value="ESPOSO">Esposo/a</SelectItem>
              <SelectItem value="HIJO">Hijo/a</SelectItem>
            </SelectContent>
          </Select>
        </div>
        {renderCamposEspecificos()}
        {vinculo && (
          <>
            <RadioGroup defaultValue="NO">
              <div className="flex items-center space-x-2">
                <Label>Es FFAA</Label>
                <RadioGroupItem value="SI" id="ffaa-si" />
                <Label htmlFor="ffaa-si">Sí</Label>
                <RadioGroupItem value="NO" id="ffaa-no" />
                <Label htmlFor="ffaa-no">No</Label>
              </div>
            </RadioGroup>
            <RadioGroup defaultValue="NO">
              <div className="flex items-center space-x-2">
                <Label>Estuvo o Está detenido</Label>
                <RadioGroupItem value="SI" id="detenido-si" />
                <Label htmlFor="detenido-si">Sí</Label>
                <RadioGroupItem value="NO" id="detenido-no" />
                <Label htmlFor="detenido-no">No</Label>
              </div>
            </RadioGroup>
          </>
        )}
        <Button type="submit" className="w-full">Enviar</Button>
      </CardContent>
    </Card>
  )
}
