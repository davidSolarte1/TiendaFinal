<?php

require 'config/config.php';
require 'config/database.php';
require 'vendor/autoload.php';
MercadoPago\SDK::setAccessToken(TOKEN_MP);

$preference = new MercadoPago\Preference();
$productos_mp = array();



$db = new Database();
$con = $db->conectar();

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

//print_r($_SESSION);

$lista_carrito = array();

if($productos != null){
    foreach($productos as $clave => $cantidad){
        $sql = $con->prepare("SELECT id,nombre,precio,descuento, $cantidad AS cantidad FROM productos WHERE id=? AND activo=1");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);

    }
}else{
    header("Location: index.php");
    exit;
}



//session_destroy();
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

    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID;?>&currency=<?php echo CURRENCY?>" data-sdk-integration-source="button-factory"></script>

    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body>
    <?php include 'menu.php'?>
    <!--Contenido-->
    <main>
        <div class="container">

            <div class="row">
                <div class="col-6">
                    <h4>Detalles de pago</h4>
                    <div class="row">
                        <div class="col-12">
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>
                
                    <div class="row">
                        <div class="col-12">
                            <div class="checkout-btn"></div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($lista_carrito == null){
                                    echo '<tr><td colspan="5" class="text-center"><b>Lista vacia</b></td></tr>';
                                }else{
        
                                    $total = 0;
                                    foreach($lista_carrito as $producto){
                                        $_id = $producto['id'];
                                        $nombre = $producto['nombre'];
                                        $precio = $producto['precio'];
                                        $descuento = $producto['descuento'];
                                        $cantidad = $producto['cantidad'];
                                        $precio_desc = $precio - (($precio*$descuento)/100);
                                        $subtotal = $cantidad * $precio_desc;
                                        $total += $subtotal;
                                        $item = new MercadoPago\Item();
                                        $item->id = $_id;
                                        $item->title = $nombre;
                                        $item->quantity = $cantidad;
                                        $item->unit_price = $precio_desc;
                                        $item->currency_id = "COP";
                                        array_push($productos_mp, $item);
                                        unset($item);
                                        ?>
                                    
                                <tr>
                                    <td><?php echo $nombre; ?></td>
                                    <td>
                                        <div id="subtotal_<?php echo $_id;?>" name="subtotal[]"><?php echo MONEDA . number_format($subtotal, 0,',','.'); ?></div>
                                    </td>                            
                                </tr>
                                <?php } ?>
        
                                <tr>
                                    
                                    <td colspan="2">
                                        <p class="h3 text-end" id="total"><?php echo MONEDA . number_format($total, 0,',','.'); ?></p>
                                    </td>
                                </tr>
                            </tbody>
                            <?php } ?> 
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php 
$preference->items = $productos_mp;
$preference->back_urls = array(
    "success" => "http://localhost/TiendaFinal/clases/captura_mp.php",
    "failure" => "http://localhost/TiendaFinal/fallo.php"
);

$preference->auto_return = "approved";
$preference->binary_mode = true; 

$preference->save();

?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    

    <script src="https://www.paypal.com/sdk/js?client-id=Aa7DJssZI2_UHgMjnvsqQlqmsRmfRvTsANfb6GVIBJfIG4if1U1_Z2mdNMjqiQxKG5LnXkh_J071oFbr&currency=USD" data-sdk-integration-source="button-factory"></script>
  <script>
    function initPayPalButton() {
      paypal.Buttons({
        style: {
          shape: 'pill',
          color: 'blue',
          layout: 'vertical',
          label: 'pay',
          
        },

        createOrder: function(data, actions) {
          return actions.order.create({
            purchase_units: [{
                amount:{
                    value : <?php echo $total;?>
                }
            }]
          });
        },

        onCancel: function(data){
            alert("Pago Cancelado")
        },

        onApprove: function(data, actions) {
            let url = 'clases/captura.php'
          return actions.order.capture().then(function(detalles) {
            console.log(detalles);
            let url = 'clases/captura.php'
            return fetch(url,{
                method: 'post',
                headers: {
                    'content-type': 'application/json'
                },
                body: JSON.stringify({

                    detalles: detalles
                })
            }).then(function(response){
                window.location.href = "completado.php?key=" +detalles['id']; //datos['detalles']['id']
            })
          });
        },

        onError: function(err) {
          console.log(err);
        }
      }).render('#paypal-button-container');

      const mp = new MercadoPago('TEST-7d7691fd-0d98-49ca-ba37-1c5fa9dd17ed',{
            locale: 'es-CO'
        });

        mp.checkout({
            preference:{
                id:'<?php echo $preference->id;?>'
            },
            render:{
                container: '.checkout-btn',
                label: 'Pagar con Mercado Pago'
            }
        })
    }
    initPayPalButton();
  </script>


</body>
</html>