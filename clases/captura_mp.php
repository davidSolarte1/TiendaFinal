<?php

require '../config/config.php';
require '../config/database.php';
$db = new Database();
$con = $db->conectar();

$idTransaccion = isset($_GET['payment_id']) ? $_GET['payment_id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

if($idTransaccion != ''){
    $fecha = date("Y-m-d H:i:s");
    $total = isset($_SESSION['carrito']['total']) ? $_SESSION['carrito']['total'] : 0;
    $idCliente = $_SESSION['user_cliente'];
    $sql = $con->prepare("SELECT email FROM clientes WHERE id=? AND estatus=1");
    $sql->execute([$idCliente]);
    $row_cliente = $sql->fetch(PDO::FETCH_ASSOC);
    $email = $row_cliente['email'];

    $sql = $con->prepare("INSERT INTO compra (id_transaccion,fecha,status,email,id_cliente,	total) VALUES (?,?,?,?,?,?)");
    $sql->execute([$idTransaccion,$fecha,$status,$email,$idCliente,	$total]);
    $id = $con->lastInsertId();

    if ($id > 0){
        $productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
        if($productos != null){
            foreach($productos as $clave => $cantidad){
                $sqlProd = $con->prepare("SELECT id,nombre,precio,descuento FROM productos WHERE id=? AND activo=1");
                $sqlProd->execute([$clave]);
                $row_prod = $sqlProd->fetch(PDO::FETCH_ASSOC);
                
                $precio = $row_prod['precio'];
                $descuento = $row_prod['descuento'];
                $precio_desc = $precio - (($precio*$descuento)/100);

                $sql_insert = $con->prepare("INSERT INTO detalle_compra (id_compra, id_producto, nombre, precio, cantidad) VALUES (?,?,?,?,?)");
                $sql_insert->execute([$id, $row_prod['id'], $row_prod['nombre'], $precio_desc, $cantidad]);

            }

            require 'Mailer.php';

            $asunto ="Detalles de su pedido";
            $cuerpo = '<h4>Gracias por su compra</h4>';
            $cuerpo .= '<p> El ID de su compra es <b>'. $idTransaccion. '</b></p>';

            $mailer = new Mailer();
            $mailer->enviarEmail($email, $asunto, $cuerpo);
        }

        unset($_SESSION['carrito']);
        header("Location: " . SITE_URL . "/completado.php?key=" . $idTransaccion);
    }

}