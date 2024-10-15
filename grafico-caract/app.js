new Vue({
    el: '#app',
    data: {
        parteSeleccionada: null,
        parteSeleccionadaEspanol: '',
        tipoMarca: '',
        observaciones: '',
        bodyPartsMap: {
            'head': 'Cabeza',
            'face': 'Cara',
            'neck': 'Cuello',
            'shoulder-left': 'Hombro izquierdo',
            'shoulder-right': 'Hombro derecho',
            'arm-left': 'Brazo izquierdo',
            'arm-right': 'Brazo derecho',
            'forearm-left': 'Antebrazo izquierdo',
            'forearm-right': 'Antebrazo derecho',
            'chest-left': 'Pecho izquierdo',
            'chest-right': 'Pecho derecho',
            'belly-left': 'Vientre izquierdo',
            'belly-right': 'Vientre derecho',
            'belly': 'Vientre',
            'ribs-left': 'Costillas izquierdas',
            'ribs-right': 'Costillas derechas',
            'thigh-left': 'Muslo izquierdo',
            'thigh-right': 'Muslo derecho',
            'innerthigh-left': 'Cara interna del muslo izquierdo',
            'innerthigh-right': 'Cara interna del muslo derecho',
            'feet-left': 'Pie izquierdo',
            'right-feet': 'Pie derecho',
            'calf-left': 'Pantorrilla izquierda',
            'calf-right': 'Pantorrilla derecha',
            'knee-left': 'Rodilla izquierda',
            'knee-right': 'Rodilla derecha',
            'elbow-right': 'Codo derecho',
            'hand-right': 'Mano derecha',
            'elbow-left': 'Codo izquierdo',
            'hands-left': 'Mano izquierda',
            'armback-left': 'Parte posterior del brazo izquierdo',
            'leg-left': 'Pierna izquierda',
            'buttock': 'Glúteos',
            'loin': 'Lumbar',
            'column': 'Columna',
            'head-back': 'Parte posterior de la cabeza',
            'nape': 'Nuca',
            'armback-right': 'Parte posterior del brazo derecho',
            'leg-right': 'Pierna derecha',
            'back-right': 'Espalda derecha',
            'clavicule-right': 'Clavícula derecha',
            'back-left': 'Espalda izquierda',
            'clavicule-left': 'Clavícula izquierda',
            'genitalia': 'Genitales'
        }
    },
    methods: {
        selectPart(partId) {
            this.parteSeleccionada = partId;
            this.parteSeleccionadaEspanol = this.bodyPartsMap[partId] || partId;
            console.log('Parte seleccionada:', this.parteSeleccionadaEspanol);
        },
        guardarMarca() {
            if (!this.parteSeleccionada) {
                alert('Por favor, seleccione una parte del cuerpo antes de guardar.');
                return;
            }

            fetch('guardar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    parte: this.parteSeleccionada,
                    tipo: this.tipoMarca,
                    observaciones: this.observaciones
                }),
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    this.tipoMarca = '';
                    this.observaciones = '';
                    // Mantenemos this.parteSeleccionada para que siga resaltada
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('Hubo un error al guardar la marca.');
                });
        }
    }
});