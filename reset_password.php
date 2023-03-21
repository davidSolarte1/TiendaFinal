<?php

require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';

$user_id = $_GET['id'] ?? $_POST['user_id'] ?? '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if($user_id == '' || $token == ''){
    header("Location: index.php");
    exit;
}

$db = new Database();
$con = $db->conectar();

$errors = [];

if(!verificaTokenRequest($user_id,$token,$con)){
    echo "No se pudo verificar la informacion";
    exit;
}

if(!empty($_POST)){
    
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if(esNulo([$user_id,$token,$password,$repassword])){
        $errors[] = "Debe llenar todos los campos";
    }


    if(!validaPassword($password, $repassword)){
        $errors[] = "Las contraseñas no coinciden";
    }

    if(count($errors) == 0){
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        if(actualizaPassword($user_id,$pass_hash,$con)){
            echo "Contraseña Modificada.<br><a href='login.php'>Iniciar Sesion </a>";
            exit;
        }else{
            $errors[] = "Error al modificar contraseña, intentalo nuevamente";
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
        <h3>Cambiar Contraseña</h3>

        <?php mostrarMensajes($errors);?>

        <form class="row g-3" autocomplete="off" action="reset_password.php" method="post">

            <input type="hidden" name="user_id" id="user_id" value="<?= $user_id?>">
            <input type="hidden" name="token" id="token" value="<?= $token?>">

            <div class="form-floating">
                <input class="form-control"type="password" name="password" id="password" placeholder="Nueva Contraseña" >
                <label for="password">Nueva Contraseña</label>
            </div>
            
            <div class="form-floating">
                <input class="form-control"type="password" name="repassword" id="repassword" placeholder="Confirmar Contraseña" >
                <label for="repassword">Confirmar Contraseña</label>
            </div>

            
            <div class="d-grid gap-3 col-12">
                <button type="submit" class="btn btn-primary">Solicitar</button>
            </div>

            <hr>
    
            <div class="col-12">
                <a href="login.php">Iniciar sesion</a>
            </div>

        </form>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>