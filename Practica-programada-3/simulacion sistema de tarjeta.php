<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Cuenta de Tarjeta de Crédito</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 600px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #333;
            color: #fff;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .summary {
            margin-top: 15px;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Estado de Cuenta</h1>

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
            $detalleTransacciones = "";

            // Recorrer el arreglo de transacciones y calcular el monto total
            foreach ($transacciones as $transaccion) {
                $detalleTransacciones .= "<tr>
                    <td>{$transaccion->id}</td>
                    <td>{$transaccion->descripcion}</td>
                    <td>₡" . number_format($transaccion->monto, 2) . "</td>
                </tr>";
                $montoContado += $transaccion->monto;
            }

            // Calcular el interés del 2.6%
            $montoConInteres = $montoContado * 1.026;

            // Calcular el cashback del 0.1% del monto contado
            $cashBack = $montoContado * 0.001;

            // Calcular el monto final a pagar después del interés
            $montoFinal = $montoConInteres - $cashBack;

            // Mostrar el estado de cuenta
            echo "<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Monto (₡)</th>
                        </tr>
                    </thead>
                    <tbody>
                        $detalleTransacciones
                    </tbody>
                </table>";

            echo "<div class='summary'>
                    <p class='total'>Monto Total de Contado: ₡" . number_format($montoContado, 2) . "</p>
                    <p class='total'>Monto Total con Interés (2.6%): ₡" . number_format($montoConInteres, 2) . "</p>
                    <p class='total'>Cashback (0.1%): ₡" . number_format($cashBack, 2) . "</p>
                    <p class='total'>Monto Final a Pagar: ₡" . number_format($montoFinal, 2) . "</p>
                </div>";

            // Generar el archivo de estado de cuenta
            $estadoCuentaTexto = "=== Estado de Cuenta ===\n";
            foreach ($transacciones as $transaccion) {
                $estadoCuentaTexto .= "ID: {$transaccion->id} - Descripción: {$transaccion->descripcion} - Monto: ₡{$transaccion->monto}\n";
            }
            $estadoCuentaTexto .= "\nMonto Total de Contado: ₡" . number_format($montoContado, 2) . "\n";
            $estadoCuentaTexto .= "Monto Total con Interés (2.6%): ₡" . number_format($montoConInteres, 2) . "\n";
            $estadoCuentaTexto .= "Cashback (0.1%): ₡" . number_format($cashBack, 2) . "\n";
            $estadoCuentaTexto .= "Monto Final a Pagar: ₡" . number_format($montoFinal, 2) . "\n";

            file_put_contents("estado_cuenta.txt", $estadoCuentaTexto);
            echo "<p>El estado de cuenta ha sido guardado en 'estado_cuenta.txt'.</p>";
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

    </div>
</body>
</html>
