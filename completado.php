<?php

require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$id_transaccion = isset($_GET['key']) ? $_GET['key'] : 0;

$error = '';
if($id_transaccion == ''){
    $error = 'Error al procesar la peticion';
}else{
    $sql = $con->prepare("SELECT count(id) FROM compra WHERE id_transaccion=? AND status = ?");
        $sql->execute([$id_transaccion,'COMPLETED']);

        if($sql->fetchColumn()>0){

            $sql = $con->prepare("SELECT id,fecha,email,total FROM compra WHERE id_transaccion =? AND status = ? LIMIT 1");
            $sql->execute([$id_transaccion, 'COMPLETED']);
            $row = $sql->fetch(PDO::FETCH_ASSOC);

            $idCompra = $row['id'];
            $total   = $row['total'];
            $fecha = $row['fecha'];

            $sqlDet = $con->prepare("SELECT nombre, precio, cantidad FROM detalle_compra WHERE id_compra = ?");
            $sqlDet->execute([$idCompra]);
        }else {
            $error = 'Error al comprobar la compra';
        }
}
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
            <?php if(strlen($error) > 0) { ?>
                <div class="row">
                    <div class="col">
                        <h3>
                            <?php echo $error; ?>
                        </h3>
                    </div>
                </div>

                <?php } else { ?>
                    <div class="row">
                        <div class="col">
                            <b>Folio de la compra: </b><?php echo $id_transaccion; ?><br>
                            <b>Fecha de compra: </b><?php echo $fecha; ?><br>
                            <b>Total: </b><?php echo MONEDA . number_format($total,0,',','.'); ?><br>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Cantidad</th>
                                        <th>Producto</th>
                                        <th>Importe</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while($row_det = $sqlDet-> fetch(PDO::FETCH_ASSOC)) { $importe = $row_det['precio']* $row_det['cantidad']; ?>
                                        <tr>
                                            <td>
                                                <?php echo $row_det['cantidad']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row_det['nombre']; ?>
                                            </td>
                                            <td>
                                                <?php echo $importe; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
            <?php } ?>
        </div>
    </main>
</body>
</html>