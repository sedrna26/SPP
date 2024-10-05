
<div class="tab-pane fade show active" id="tab2">
    <div class="content ">
        <div class="row m-auto ">
            <div class="container ">
                <h1 class="mb-4">Situación Legal</h1>
                <form>
                    <div class="mb-3 row">
                        <label for="fechaDetencion" class="col-sm-3 col-form-label">Fecha de detención:</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="fechaDetencion">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="dependencia" class="col-sm-3 col-form-label">Dependencia:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="dependencia">
                        </div>
                        <label for="motivoTraslado" class="col-sm-2 col-form-label">Motivo del traslado:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="motivoTraslado">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="situacionLegal" class="col-sm-3 col-form-label">Situación Legal:</label>
                        <div class="col-sm-9">
                            <select class="form-select" id="situacionLegal">
                                <option>Procesado</option>
                                <option>Penado</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="causa" class="col-sm-3 col-form-label">Causa:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="causa">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="juzgado" class="col-sm-3 col-form-label">Juzgado:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="juzgado">
                        </div>
                        <label for="prontuario" class="col-sm-2 col-form-label">Prontuario Nº:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="prontuario">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="condena" class="col-sm-3 col-form-label">Condena:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="condena">
                        </div>
                        <label for="vence" class="col-sm-2 col-form-label">Vence:</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="vence">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="reincidencia" class="col-sm-3 col-form-label">Reincidencia:</label>
                        <div class="col-sm-9">
                            <select class="form-select" id="reincidencia">
                                <option>Primario</option>
                                <option>Reiterante</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="cantidadCondenas" class="col-sm-3 col-form-label">Cantidad de condenas:</label>
                        <div class="col-sm-3">
                            <input type="number" class="form-control" id="cantidadCondenas">
                        </div>
                        <label for="ultimaCondena" class="col-sm-3 col-form-label">Última condena, fecha de egreso:</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="ultimaCondena">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="st" class="col-sm-3 col-form-label">S.T.:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="st">
                        </div>
                        <label for="la" class="col-sm-2 col-form-label">L. A.:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="la">
                        </div>
                        <label for="lc" class="col-sm-2 col-form-label">L. C.:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="lc">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="causasPendientes" class="col-sm-3 col-form-label">Causas pendientes de resolución:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="causasPendientes">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            </div>
        </div> 
    </div>
</div>        