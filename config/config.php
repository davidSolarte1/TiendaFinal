<?php

define("CLIENT_ID", "Aa7DJssZI2_UHgMjnvsqQlqmsRmfRvTsANfb6GVIBJfIG4if1U1_Z2mdNMjqiQxKG5LnXkh_J071oFbr");
define("CURRENCY", "USD");
define("KEY_TOKEN", "WazaSA.BEL-12");
define("MONEDA", "$");

session_start();

$num_cart = 0;
if(isset($_SESSION['carrito']['productos'])){
    $num_cart = count($_SESSION['carrito']['productos']);
}
?>