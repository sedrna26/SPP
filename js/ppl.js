// Función para mostrar u ocultar la sección de agregar nuevo delito
function toggleNewDelitoSection() {
    const newDelitoSection = document.getElementById('newDelitoSection');
    if (newDelitoSection.style.display === 'none') {
        newDelitoSection.style.display = 'block';
    } else {
        newDelitoSection.style.display = 'none';
    }
}

function previewImage(event) {
    const input = event.target;
    const imagePreview = document.getElementById('Foto');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            imagePreview.src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]); // Leer archivo como Data URL
    }
}

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const output = document.getElementById('Foto');
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

let selectedCausas = [];

function updateSelectedCausas() {
    selectedCausas = [];
    let checkboxes = document.querySelectorAll('input[name="causas[]"]:checked');
    checkboxes.forEach(checkbox => {
        selectedCausas.push(checkbox.nextElementSibling.innerText);
    });

    // Actualizar el texto con las causas seleccionadas
    let selectedText = selectedCausas.join(', ') || 'Selecciona hasta 4 causas.';
    document.getElementById('selected-causas-text').innerText = 'Causas seleccionadas: ' + selectedText;

    // Si se seleccionan más de 4 causas, desmarcar la última
    if (selectedCausas.length > 4) {
        alert("Solo puedes seleccionar hasta 4 causas.");
        checkboxes[checkboxes.length - 1].checked = false;
        selectedCausas.pop();
    }
}

// Esta función es para asegurarse de que no se seleccionen más de 4 causas.
function updateSelectedCausas() {
    var checkboxes = document.querySelectorAll('input[name="causas[]"]:checked');
    if (checkboxes.length > 4) {
        alert('Solo puedes seleccionar hasta 4 causas.');
        checkboxes[checkboxes.length - 1].checked = false;
    }
}

function toggleNewLocation() {
    const direccionp = document.getElementById('direccionp').value;
    const newLocationSection = document.getElementById('newLocationSection');
    if (direccionp === 'new') {
        style = "color: green;"
        newLocationSection.style.display = 'block';
    } else {
        newLocationSection.style.display = 'none';
    }
}

function toggleViolenciaSection(selectedDelito) {
    const abusoSection = document.getElementById('abusoSection');

    // Cambia "1" por el ID real de tu delito de violencia de género
    if (selectedDelito === "1") { // Cambia esto por el ID correcto
        abusoSection.style.display = 'block';
    } else {
        abusoSection.style.display = 'none';
    }
}

function toggleNewjuez() {
    const newjuezSection = document.getElementById('newjuezSection');
    const selectedjuez = document.getElementById('id_juzgado').value;

    // Verificar el valor de selectedjuez
    console.log("Valor seleccionado: ", selectedjuez); // Para verificar si el valor es correcto

    // Mostrar u ocultar la sección según la selección
    if (selectedjuez === 'new') {
        newjuezSection.style.display = 'block';
    } else {
        newjuezSection.style.display = 'none';
    }
}

function agregarAsteriscoYValidarFormulario(formId) {
    // Seleccionar el formulario por su ID
    const form = document.getElementById(formId);

    // Seleccionar todos los campos requeridos dentro del formulario
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        // Añadir el asterisco rojo al label correspondiente
        const label = form.querySelector(`label[for='${field.id}']`);
        if (label) {
            label.innerHTML += '<span style="color: red;">*</span>';
        }

        // Añadir evento para validar antes de enviar
        field.addEventListener('invalid', (event) => {
            event.preventDefault(); // Prevenir el mensaje de error predeterminado
            alert(`El campo ${label ? label.innerText : ''} es obligatorio.`);
        });
    });

    // Validar el formulario al enviar
    form.addEventListener('submit', (event) => {
        let isValid = true;

        requiredFields.forEach(field => {
            if (field.value.trim() === '') {
                isValid = false;
                field.classList.add('is-invalid'); // Clase para resaltar el campo inválido
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            event.preventDefault(); // Prevenir el envío si hay campos vacíos
            alert('Por favor complete todos los campos requeridos.');
        }
    });
}

// Llama a la función pasando el ID de tu formulario
window.onload = function () {
    agregarAsteriscoYValidarFormulario('personaForm'); // Usar el ID de tu formulario
};

function mostrarCamposDefensor() {
    const tieneDefensorSelect = document.getElementById('tiene_defensor');
    const nombreDefensorDiv = document.getElementById('nombreDefensorDiv');
    const tieneComDefensorDiv = document.getElementById('tieneComDefensorDiv');

    // Mostrar u ocultar los campos basados en la selección
    if (tieneDefensorSelect.value === 'si') {
        nombreDefensorDiv.style.display = 'block';
        tieneComDefensorDiv.style.display = 'block';
    } else {
        nombreDefensorDiv.style.display = 'none';
        tieneComDefensorDiv.style.display = 'none';
    }
}

// Llama a la función pasando el ID de tu formulario
window.onload = function () {
    agregarAsteriscoYValidarFormulario('personaForm'); // Usar el ID de tu formulario
    mostrarCamposDefensor(); // Llamar la función al cargar
    document.getElementById('tiene_defensor').addEventListener('change', mostrarCamposDefensor); // Añadir evento de cambio
};

function calcularEdad() {
    const fechanacInput = document.getElementById('fechanac');
    const edadInput = document.getElementById('edad');

    // Obtener la fecha de nacimiento
    const fechaNacimiento = new Date(fechanacInput.value);
    const hoy = new Date();

    // Calcular la diferencia en años
    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
    const mes = hoy.getMonth() - fechaNacimiento.getMonth();

    // Ajustar la edad si el cumpleaños no ha ocurrido aún este año
    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
        edad--;
    }

    // Validar que la edad esté entre 18 y 70
    if (edad < 18 || edad > 70) {
        edadInput.value = ''; // Limpiar el campo si la edad no es válida
    } else {
        edadInput.value = edad; // Asignar la edad al campo correspondiente
    }


}