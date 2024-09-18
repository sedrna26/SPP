'use client'

import { useState } from 'react'
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group"
import { Calendar } from "@/components/ui/calendar"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { CalendarIcon } from "lucide-react"
import { format } from "date-fns"
import { es } from "date-fns/locale"

export default function FormularioFamiliar() {
  const [fechaNacimiento, setFechaNacimiento] = useState<Date>()
  const [fallecio, setFallecio] = useState("NO")
  const [fechaFallecimiento, setFechaFallecimiento] = useState<Date>()

  return (
    <Card className="w-full max-w-3xl mx-auto">
      <CardHeader>
        <CardTitle>Formulario Familiar</CardTitle>
      </CardHeader>
      <CardContent>
        <form className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="vinculoFamiliar">Vínculo Familiar</Label>
            <Select>
              <SelectTrigger id="vinculoFamiliar">
                <SelectValue placeholder="Seleccione el vínculo familiar" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="padre">Padre</SelectItem>
                <SelectItem value="madre">Madre</SelectItem>
                <SelectItem value="hermano">Hermano/a</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-2">
            <Label htmlFor="apellido">Apellido</Label>
            <Input id="apellido" placeholder="Ingrese el apellido" />
          </div>

          <div className="space-y-2">
            <Label htmlFor="nombre">Nombre</Label>
            <Input id="nombre" placeholder="Ingrese el nombre" />
          </div>

          <div className="space-y-2">
            <Label htmlFor="fechaNacimiento">Fecha de Nacimiento</Label>
            <Popover>
              <PopoverTrigger asChild>
                <Button
                  variant={"outline"}
                  className={`w-full justify-start text-left font-normal ${!fechaNacimiento && "text-muted-foreground"}`}
                >
                  <CalendarIcon className="mr-2 h-4 w-4" />
                  {fechaNacimiento ? format(fechaNacimiento, "PPP", { locale: es }) : <span>Seleccione una fecha</span>}
                </Button>
              </PopoverTrigger>
              <PopoverContent className="w-auto p-0">
                <Calendar
                  mode="single"
                  selected={fechaNacimiento}
                  onSelect={setFechaNacimiento}
                  initialFocus
                />
              </PopoverContent>
            </Popover>
          </div>

          <div className="space-y-2">
            <Label htmlFor="edad">Edad</Label>
            <Input id="edad" type="number" placeholder="Ingrese la edad" />
          </div>

          <div className="space-y-2">
            <Label htmlFor="pais">País</Label>
            <Input id="pais" placeholder="Ingrese el país" />
          </div>

          <div className="space-y-2">
            <Label htmlFor="provincia">Provincia</Label>
            <Input id="provincia" placeholder="Ingrese la provincia" />
          </div>

          <div className="space-y-2">
            <Label htmlFor="departamento">Departamento</Label>
            <Input id="departamento" placeholder="Ingrese el departamento" />
          </div>

          <div className="space-y-2">
            <Label htmlFor="direccion">Dirección</Label>
            <Input id="direccion" placeholder="Ingrese la dirección" />
          </div>

          <div className="space-y-2">
            <Label>Género</Label>
            <Select>
              <SelectTrigger id="genero">
                <SelectValue placeholder="Seleccione el género" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="varon">Varón</SelectItem>
                <SelectItem value="mujer">Mujer</SelectItem>
                <SelectItem value="transMujer">Trans Mujer</SelectItem>
                <SelectItem value="transVaron">Trans Varón</SelectItem>
                <SelectItem value="noBinario">No Binario</SelectItem>
                <SelectItem value="otro">Otro</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-2">
            <Label>Estado Civil</Label>
            <Select>
              <SelectTrigger id="estadoCivil">
                <SelectValue placeholder="Seleccione el estado civil" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="soltero">Soltero/a</SelectItem>
                <SelectItem value="casado">Casado/a</SelectItem>
                <SelectItem value="viudo">Viudo/a</SelectItem>
                <SelectItem value="divorciado">Divorciado/a</SelectItem>
                <SelectItem value="separado">Separado/a de Hecho</SelectItem>
                <SelectItem value="concubino">Concubino/a</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-2">
            <Label>¿Es FFAA?</Label>
            <RadioGroup defaultValue="NO">
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="SI" id="ffaaSi" />
                <Label htmlFor="ffaaSi">Sí</Label>
              </div>
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="NO" id="ffaaNo" />
                <Label htmlFor="ffaaNo">No</Label>
              </div>
            </RadioGroup>
          </div>

          <div className="space-y-2">
            <Label>¿Estuvo o Está detenido?</Label>
            <RadioGroup defaultValue="NO">
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="SI" id="detenidoSi" />
                <Label htmlFor="detenidoSi">Sí</Label>
              </div>
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="NO" id="detenidoNo" />
                <Label htmlFor="detenidoNo">No</Label>
              </div>
            </RadioGroup>
          </div>

          <div className="space-y-2">
            <Label>¿Falleció?</Label>
            <RadioGroup value={fallecio} onValueChange={setFallecio}>
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="SI" id="fallecioSi" />
                <Label htmlFor="fallecioSi">Sí</Label>
              </div>
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="NO" id="fallecioNo" />
                <Label htmlFor="fallecioNo">No</Label>
              </div>
            </RadioGroup>
          </div>

          {fallecio === "SI" && (
            <>
              <div className="space-y-2">
                <Label htmlFor="fechaFallecimiento">Fecha de Fallecimiento</Label>
                <Popover>
                  <PopoverTrigger asChild>
                    <Button
                      variant={"outline"}
                      className={`w-full justify-start text-left font-normal ${!fechaFallecimiento && "text-muted-foreground"}`}
                    >
                      <CalendarIcon className="mr-2 h-4 w-4" />
                      {fechaFallecimiento ? format(fechaFallecimiento, "PPP", { locale: es }) : <span>Seleccione una fecha</span>}
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent className="w-auto p-0">
                    <Calendar
                      mode="single"
                      selected={fechaFallecimiento}
                      onSelect={setFechaFallecimiento}
                      initialFocus
                    />
                  </PopoverContent>
                </Popover>
              </div>

              <div className="space-y-2">
                <Label htmlFor="causaFallecimiento">Causa de Fallecimiento</Label>
                <Input id="causaFallecimiento" placeholder="Ingrese la causa de fallecimiento" />
              </div>
            </>
          )}

          <Button type="submit" className="w-full">Enviar</Button>
        </form>
      </CardContent>
    </Card>
  )
}
