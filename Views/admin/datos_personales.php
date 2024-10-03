
<div class="tab-pane fade show active" id="tab1">
    <div class="content ">
        <div class="row m-auto ">
            <div class="col-sm">
                <h1 class="">Datos Personales</h1>
                <form>
                    <div class="mb-3 row">
                        <label for="horaEntrevista" class="col-sm-2 col-form-label">Hora de la entrevista:</label>
                        <div class="col-sm-9">
                            <input type="time" class="form-control" id="horaEntrevista">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="fechaIngreso" class="col-sm-2 col-form-label">Fecha de ingreso:</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="fechaIngreso">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="nombreCompleto" class="col-sm-2 col-form-label">Apellidos y Nombres:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nombreCompleto">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="fechaNacimiento" class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" id="fechaNacimiento">
                        </div>
                        <label for="edad" class="col-sm-1 col-form-label">Edad:</label>
                        <div class="col-sm-2">
                            <input type="number" class="form-control" id="edad">
                        </div>
                        <label for="apodo" class="col-sm-1 col-form-label">Apodo:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="apodo">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="lugar" class="col-sm-2 col-form-label">Lugar:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="lugar">
                        </div>
                        <label for="nacionalidad" class="col-sm-2 col-form-label">Nacionalidad:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="nacionalidad">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="domicilio" class="col-sm-2 col-form-label">Domicilio:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="domicilio">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="departamento" class="col-sm-2 col-form-label">Departamento / Localidad:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="departamento">
                        </div>
                        <label for="provincia" class="col-sm-1 col-form-label">Provincia:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="provincia">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="dni" class="col-sm-2 col-form-label">D.N.I.:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="dni">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Estado Civil:</label>
                        <div class="col-sm-9">
                            <select class="form-select" id="estadoCivil">
                                <option>Casado/a</option>
                                <option>Divorciado/a</option>
                                <option>Soltero/a</option>
                                <option>Viudo/a</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="profesion" class="col-sm-2 col-form-label">Profesión u Oficio:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="profesion">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">¿Se encontraba trabajando?</label>
                        <div class="col-sm-9">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="trabajando" id="trabajandoSi" value="SI">
                                <label class="form-check-label" for="trabajandoSi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="trabajando" id="trabajandoNo" value="NO">
                                <label class="form-check-label" for="trabajandoNo">NO</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Fotografía:</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="fotografia">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            
            </div>
        </div>
    </div>
</div>
