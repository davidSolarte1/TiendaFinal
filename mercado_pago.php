<?php

require 'vendor/autoload.php';

MercadoPago\SDK::setAccessToken('TEST-4926848938194335-032019-02e1d8ccad1a3f5fa99a9c997eb99a4c-292394869');

$preference = new MercadoPago\Preference();

$item = new MercadoPago\Item();
$item->id = '0001';
$item->title = 'Producto tienda';
$item->quantity = 1;
$item->unit_price = 12000;
$item->currency_id = "COP";

$preference->items = array($item);


$preference->back_urls = array(
    "success" => "http://localhost/TiendaFinal/captura.php",
    "failure" => "http://localhost/TiendaFinal/fallo.php"
);

$preference->auto_return = "approved";
$preference->binary_mode = true; 

$preference->save();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body>
    <h3>Mercado Pago</h3>

    <div class="checkout-btn"></div>

    <script>
        const mp = new MercadoPago('TEST-7d7691fd-0d98-49ca-ba37-1c5fa9dd17ed',{
            locale: 'es-CO'
        });

        mp.checkout({
            preference:{
                id:'<?php echo $preference->id;?>'
            },
            render:{
                container: '.checkout-btn',
                label: 'Pagar con MP'
            }
        })
    </script>
</body>
</html>