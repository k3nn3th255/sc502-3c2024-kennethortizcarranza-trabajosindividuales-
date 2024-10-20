// script.js
document.getElementById('calcularBtn').addEventListener('click', function() {
    const salarioBruto = parseFloat(document.getElementById('salarioBruto').value);

    if (isNaN(salarioBruto) || salarioBruto <= 0) {
        alert('Por favor ingrese un salario bruto válido.');
        return;
    }

    // Cálculo de cargas sociales (9.34% del salario bruto en Costa Rica)
    const cargasSociales = salarioBruto * 0.0934;

    // Cálculo de impuesto sobre la renta
    let impuestoRenta = 0;
    if (salarioBruto > 941000) {
        impuestoRenta = (salarioBruto - 941000) * 0.15;
    } else if (salarioBruto > 817000) {
        impuestoRenta = (salarioBruto - 817000) * 0.10;
    }

    // Salario neto
    const salarioNeto = salarioBruto - cargasSociales - impuestoRenta;

    // Mostrar resultados en la página
    document.getElementById('cargasSociales').textContent = `Cargas Sociales: ₡${cargasSociales.toFixed(2)}`;
    document.getElementById('impuestoRenta').textContent = `Impuesto sobre la Renta: ₡${impuestoRenta.toFixed(2)}`;
    document.getElementById('salarioNeto').textContent = `Salario Neto: ₡${salarioNeto.toFixed(2)}`;
});
