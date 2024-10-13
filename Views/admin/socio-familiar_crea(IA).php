
    <div class="container mt-4">
        <h4 class="mb-3">Datos inherentes a la situación social y familiar</h4>
        
        <!-- Familiares de FF.AA. -->
        <div class="form-group">
            <label>Familiares de FF.AA.: </label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="ffaa" id="ffaa_si" value="SI">
                <label class="form-check-label" for="ffaa_si">SI</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="ffaa" id="ffaa_no" value="NO">
                <label class="form-check-label" for="ffaa_no">NO</label>
            </div>
        </div>
        
        <!-- Familiares Detenidos -->
        <div class="form-group">
            <label>Familiares Detenidos:</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="detenidos" id="detenidos_si" value="SI">
                <label class="form-check-label" for="detenidos_si">SI</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="detenidos" id="detenidos_no" value="NO">
                <label class="form-check-label" for="detenidos_no">NO</label>
            </div>
        </div>

        <!-- Datos del padre -->
        <hr>
        <h6 class="font-weight-bold">Padre</h6>
        <div class="form-group">
            <label>Vivo: </label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="padre_vivo" id="padre_vivo_si" value="SI">
                <label class="form-check-label" for="padre_vivo_si">SI</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="padre_vivo" id="padre_vivo_no" value="NO">
                <label class="form-check-label" for="padre_vivo_no">NO</label>
            </div>
        </div>

        <div class="form-group">
            <label>Nombre y Apellido:</label>
            <input type="text" class="form-control" name="padre_nombre">
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Edad:</label>
                <input type="text" class="form-control" name="padre_edad">
            </div>
            <div class="form-group col-md-6">
                <label>Nacionalidad:</label>
                <input type="text" class="form-control" name="padre_nacionalidad">
            </div>
        </div>

        <div class="form-group">
            <label>Estado Civil:</label>
            <select class="form-control" name="padre_estado_civil">
                <option value="">Seleccione</option>
                <option value="Soltero">Soltero</option>
                <option value="Casado">Casado</option>
                <option value="Divorciado">Divorciado</option>
                <option value="Viudo">Viudo</option>
                <option value="Union Convivencial">Unión Convivencial</option>
            </select>
        </div>

        <div class="form-group">
            <label>Grado de instrucción alcanzado, profesión u oficio:</label>
            <input type="text" class="form-control" name="padre_profesion">
        </div>

        <!-- Datos de la madre -->
        <hr>
        <h6 class="font-weight-bold">Madre</h6>
        <div class="form-group">
            <label>Viva: </label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="madre_viva" id="madre_viva_si" value="SI">
                <label class="form-check-label" for="madre_viva_si">SI</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="madre_viva" id="madre_viva_no" value="NO">
                <label class="form-check-label" for="madre_viva_no">NO</label>
            </div>
        </div>

        <div class="form-group">
            <label>Nombre y Apellido:</label>
            <input type="text" class="form-control" name="madre_nombre">
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Edad:</label>
                <input type="text" class="form-control" name="madre_edad">
            </div>
            <div class="form-group col-md-6">
                <label>Nacionalidad:</label>
                <input type="text" class="form-control" name="madre_nacionalidad">
            </div>
        </div>

        <div class="form-group">
            <label>Estado Civil:</label>
            <select class="form-control" name="madre_estado_civil">
                <option value="">Seleccione</option>
                <option value="Soltera">Soltera</option>
                <option value="Casada">Casada</option>
                <option value="Divorciada">Divorciada</option>
                <option value="Viuda">Viuda</option>
                <option value="Union Convivencial">Unión Convivencial</option>
            </select>
        </div>

        <div class="form-group">
            <label>Grado de instrucción alcanzado, profesión u oficio:</label>
            <input type="text" class="form-control" name="madre_profesion">
        </div>

        <!-- Hermanos -->
        <hr>
        <h6 class="font-weight-bold">Hermanos</h6>
        <div class="form-group">
            <label>1. Nombre y Apellido:</label>
            <input type="text" class="form-control" name="hermano1_nombre">
        </div>
        <div class="form-group">
            <label>Edad:</label>
            <input type="text" class="form-control" name="hermano1_edad">
        </div>

        <!-- Repetir similar para más hermanos -->

        <!-- Grupos familiares secundarios, personas en visita, datos adicionales -->
        <!-- Código para agregar las demás secciones del formulario de manera similar -->

        <button type="submit" class="btn btn-primary">Enviar</button>
    </div>
