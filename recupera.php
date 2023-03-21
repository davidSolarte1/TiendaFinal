<?php

require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';
$db = new Database();
$con = $db->conectar();

$errors = [];

if(!empty($_POST)){

    $email = trim($_POST['email']);
 

    if(esNulo([$email])){
        $errors[] = "Debe llenar todos los campos";
    }

    if(!esEmail($email)){
        $errors[] = "La direccion de correo no es valida";
    }

    if(count($errors)==0){
        if(emailExiste($email,$con)){
            $sql = $con->prepare("SELECT usuarios.id, clientes.nombres FROM usuarios
            INNER JOIN clientes  ON usuarios.id_cliente=clientes.id
            WHERE clientes.email LIKE ? LIMIT 1");
            $sql->execute([$email]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $user_id = $row['id'];
            $nombres = $row['nombres'];

            $token = solicitaPassword($user_id,$con);

            if($token !== null){
                require 'clases/mailer.php';
                $mailer = new Mailer();

                $url = SITE_URL . '/reset_password.php?id='.$user_id.'&token='.$token; 

                $asunto ="Recuperar password - Tienda online";
                $cuerpo = "Hola $nombres: <br> Si has solicitado el cambio de tu contraseña da click en el siguiente link <a href='$url'>$url</a>.";
                $cuerpo.= "<br>Si no hiciste esta solicitud puede ignorar este correo.";

                if($mailer->enviarEmail($email,$asunto,$cuerpo)){
                    echo "<p><b>Correo Enviado</b></p>";
                    echo "<p>Hemos enviado un correo a la direccion $email para restablecer la contraseña.</p>";
                    
                    exit;
                }

            }
        }else{
            $errors[] = "No existe una cuenta asociada a esta direccion de correo";

        }
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
    <header>
        <div class="navbar navbar-expand-lg navbar-dark bg-dark ">
            <div class="container">
                <a href="index.php" class="navbar-brand ">
                    <strong>Tienda</strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

            <div class="collapse navbar-collapse" id="navbarHeader">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">Catalogo</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Contacto</a>
                    </li>
                </ul>
            </div>

            </div>
        </div>
    </header>
    <!--Contenido-->
    <main class="form-login m-auto pt-4">
        <h3>Recuperar contraseña</h3>

        <?php mostrarMensajes($errors);?>

        <form class="row g-3" autocomplete="off" action="recupera.php" method="post">

            <div class="form-floating">
                <input class="form-control"type="email" name="email" id="email" placeholder="Correo Electronico" >
                <label for="email">Correo Electronico</label>
            </div>

            <div class="d-grid gap-3 col-12">
                <button type="submit" class="btn btn-primary">Solicitar</button>
            </div>

            <hr>
    
            <div class="col-12">
                No tiene cuenta? <a href="registro.php">Registrate aqui</a>
            </div>

        </form>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>