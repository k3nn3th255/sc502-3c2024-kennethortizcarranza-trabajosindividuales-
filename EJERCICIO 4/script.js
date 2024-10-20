// script.js
function verificarEdad() {
    // Obtener el valor ingresado por el usuario
    var edad = document.getElementById("edad").value;

    // Verificar si es mayor o menor de edad
    if (edad >= 18) {
        document.getElementById("resultado").innerHTML = "Eres mayor de edad.";
    } else {
        document.getElementById("resultado").innerHTML = "Eres menor de edad.";
    }
}
