// Arreglo de objetos con información de estudiantes
const estudiantes = [
    { nombre: 'Juan', apellido: 'Pérez', nota: 85 },
    { nombre: 'Ana', apellido: 'González', nota: 92 },
    { nombre: 'Luis', apellido: 'Martínez', nota: 78 },
    { nombre: 'María', apellido: 'Rodríguez', nota: 95 },
    { nombre: 'Carlos', apellido: 'Sánchez', nota: 88 }
];

// Elementos del DOM donde mostraremos los datos
const studentListDiv = document.getElementById('student-list');
const averageGradeDiv = document.getElementById('average-grade');

// Función para recorrer el arreglo e imprimir nombres y apellidos
function mostrarEstudiantes() {
    let contenido = '<h3>Lista de Estudiantes</h3><ul>';
    estudiantes.forEach(estudiante => {
        contenido += `<li>${estudiante.nombre} ${estudiante.apellido}</li>`;
    });
    contenido += '</ul>';
    studentListDiv.innerHTML = contenido;
}

// Función para calcular el promedio de las notas
function calcularPromedio() {
    const totalNotas = estudiantes.reduce((total, estudiante) => total + estudiante.nota, 0);
    const promedio = totalNotas / estudiantes.length;
    averageGradeDiv.innerHTML = `El promedio de las notas es: ${promedio.toFixed(2)}`;
}

// Ejecutar las funciones al cargar la página
mostrarEstudiantes();
calcularPromedio();
