<?php

define("CLIENT_ID", "Aa7DJssZI2_UHgMjnvsqQlqmsRmfRvTsANfb6GVIBJfIG4if1U1_Z2mdNMjqiQxKG5LnXkh_J071oFbr");
define("TOKEN_MP", "TEST-4926848938194335-032019-02e1d8ccad1a3f5fa99a9c997eb99a4c-292394869");
define("CURRENCY", "USD");
define("KEY_TOKEN", "WazaSA.BEL-12");
define("MONEDA", "$");

define("MAIL_HOST", "smtp.gmail.com");
define("MAIL_USER", "ventastiendabarrio@gmail.com");
define("MAIL_PASS", "fhpdezstvrdvnvfn");
define("MAIL_PORT", "587");

define("SITE_URL", "http://localhost/TiendaFinal");

session_start();

$num_cart = 0;
if(isset($_SESSION['carrito']['productos'])){
    $num_cart = count($_SESSION['carrito']['productos']);
}
?>