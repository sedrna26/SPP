document.getElementById("campo").addEventListener("keyup", getPpl)

function getPpl() {
    let inputPpl = document.getElementById("campo").value
    let lista = document.getElementById("lista")

    if (inputPpl.length > 0) {
        let url = "inc/getPpl.php"
        let formData = new FormData()
        formData.append("campo", inputPpl)

        fetch(url, {
            method: "POST",
            body: formData,
            mode: "cors"
        }).then(response => response.json())
            .then(data => {
                lista.style.display = 'block'
                lista.innerHTML = data
            })
            .catch(err => console.log(err))
    } else {
        lista.style.display = 'none'
    }
}

function mostrar(id) {
    document.getElementById("campo").value = `${id}`;
    document.getElementById("lista").style.display = 'none';
}