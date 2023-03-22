<?php

require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';

$token_session = $_SESSION['token'];
$orden = $_GET['orden'] ?? null;
$token = $_GET['token'] ?? null;

if($orden == null || $token == null || $token != $token_session){
    header("Location: compras.php");
    exit;
}

$db = new Database();
$con = $db->conectar();

$sqlCompra =$con->prepare("SELECT id, id_transaccion, fecha, total FROM compra WHERE id_transaccion = ? LIMIT 1");
$sqlCompra->execute([$orden]);
$rowCompra = $sqlCompra->fetch(PDO::FETCH_ASSOC);
$idCompra = $rowCompra['id'];

$fecha = new DateTime($rowCompra['fecha']);
$fecha = $fecha->format('d/m/Y H:i:s');

$sqlDetalle = $con->prepare("SELECT id, nombre, precio, cantidad FROM detalle_compra  WHERE id_compra = ?");
$sqlDetalle->execute([$idCompra]);


?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>La Bellaquea</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://kit.fontawesome.com/b7395829bd.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include 'menu.php'?>
    <!--Contenido-->
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Detalle de la compra</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>Fecha:</strong> <?php echo $fecha; ?></p>
                            <p><strong>Ordem:</strong> <?php echo $rowCompra['id_transaccion']; ?></p>
                            <p><strong>Total:</strong> <?php echo MONEDA .' '. number_format($rowCompra['total'],0,',','.') ; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8">

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>               
                                    <th>Producto</th>                                   
                                    <th>Precio</th>                                   
                                    <th>Cantidad</th>                                   
                                    <th>Subtotal</th>                                   
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    while($row = $sqlDetalle->fetch(PDO::FETCH_ASSOC)){ 
                                        $precio = $row['precio'];
                                        $cantidad = $row['cantidad'];
                                        $subtotal = $precio * $cantidad;
                                ?>
                                <tr>
                                    <td><?php echo $row['nombre']; ?></td>
                                    <td><?php echo MONEDA .' '. number_format($precio,0,',','.'); ?></td>
                                    <td><?php echo $cantidad; ?></td>
                                    <td><?php echo MONEDA .' '. number_format($subtotal,0,',','.'); ?></td>                                
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>