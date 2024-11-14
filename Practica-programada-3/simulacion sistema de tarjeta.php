<?php

// Clase Transaccion para almacenar detalles de cada transacción
class Transaccion {
    public $id;
    public $descripcion;
    public $monto;

    public function __construct($id, $descripcion, $monto) {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->monto = $monto;
    }
}

// Arreglo para almacenar las transacciones
$transacciones = [];

// Función para registrar una nueva transacción
function registrarTransaccion($id, $descripcion, $monto) {
    global $transacciones;
    $transaccion = new Transaccion($id, $descripcion, $monto);
    array_push($transacciones, $transaccion);
}

// Función para generar el estado de cuenta
function generarEstadoDeCuenta() {
    global $transacciones;

    $montoContado = 0;
    $detalleTransacciones = "Detalles de Transacciones:\n";

    // Recorrer el arreglo de transacciones y calcular el monto total
    foreach ($transacciones as $transaccion) {
        $detalleTransacciones .= "ID: {$transaccion->id} - Descripción: {$transaccion->descripcion} - Monto: ₡{$transaccion->monto}\n";
        $montoContado += $transaccion->monto;
    }

    // Calcular el interés del 2.6%
    $montoConInteres = $montoContado * 1.026;

    // Calcular el cashback del 0.1% del monto contado
    $cashBack = $montoContado * 0.001;

    // Calcular el monto final a pagar después del interés
    $montoFinal = $montoConInteres - $cashBack;

    // Mostrar el estado de cuenta
    echo "=== Estado de Cuenta ===\n";
    echo $detalleTransacciones;
    echo "\nMonto Total de Contado: ₡" . number_format($montoContado, 2) . "\n";
    echo "Monto Total con Interés (2.6%): ₡" . number_format($montoConInteres, 2) . "\n";
    echo "Cashback (0.1%): ₡" . number_format($cashBack, 2) . "\n";
    echo "Monto Final a Pagar: ₡" . number_format($montoFinal, 2) . "\n";

    // Generar el archivo de estado de cuenta
    $estadoCuentaTexto = "=== Estado de Cuenta ===\n";
    $estadoCuentaTexto .= $detalleTransacciones;
    $estadoCuentaTexto .= "\nMonto Total de Contado: ₡" . number_format($montoContado, 2) . "\n";
    $estadoCuentaTexto .= "Monto Total con Interés (2.6%): ₡" . number_format($montoConInteres, 2) . "\n";
    $estadoCuentaTexto .= "Cashback (0.1%): ₡" . number_format($cashBack, 2) . "\n";
    $estadoCuentaTexto .= "Monto Final a Pagar: ₡" . number_format($montoFinal, 2) . "\n";

    file_put_contents("estado_cuenta.txt", $estadoCuentaTexto);
    echo "\nEl estado de cuenta ha sido guardado en 'estado_cuenta.txt'.\n";
}

// Simulación de registro de varias transacciones
registrarTransaccion(1, "Compra en supermercado", 10000);
registrarTransaccion(2, "Cena en restaurante", 25000);
registrarTransaccion(3, "Gasolina", 15000);
registrarTransaccion(4, "Compra en línea", 5000);
registrarTransaccion(5, "Pago de servicios", 8000);

// Generar el estado de cuenta
generarEstadoDeCuenta();

?>
