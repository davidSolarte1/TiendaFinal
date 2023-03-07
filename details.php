<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if($id == '' || $token == '' ){
    echo 'Error al procesar la peticion';
    exit;
}else {

    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

    if($token == $token_tmp){
         
        $sql = $con->prepare("SELECT count(id) FROM productos WHERE id=? AND activo = 1");
        $sql->execute([$id]);

        if($sql->fetchColumn()>0){
            $sql = $con->prepare("SELECT nombre,descripcion,precio, descuento FROM productos WHERE id=? AND activo = 1 LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $nombre = $row['nombre'];
            $descripcion = $row['descripcion'];
            $precio = $row['precio'];
            $descuento = $row['descuento'];
            $precio_desc = $precio - (($precio * $descuento)/100);
            $dir_images = 'images/productos/'.$id.'/';

            $rutaImg = $dir_images. 'principal.png';


            if(!file_exists($rutaImg)){
                $rutaImg = 'images/no-photo.png';
            }

            $imagenes = array();
            $dir = dir($dir_images);

            while(($archivo = $dir->read()) != false){
                if($archivo != 'principal.png' && (strpos($archivo, 'png') || strpos($archivo, 'jepg'))){  
                    $imagenes [] = $dir_images . $archivo; 
                }
            }
            $dir->close();
        }
        

    }else{
        echo 'Error al procesar la peticion----'.$token; 
        echo '___________';
        echo $token_tmp;
        exit;
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
</head>
<body>
    <header>
        <div class="navbar navbar-expand-lg navbar-dark bg-dark ">
            <div class="container">
                <a href="#" class="navbar-brand ">
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
                <a href="carrito.php" class="btn btn-primary">Carrito</a>
            </div>

            </div>
        </div>
    </header>
    <!--Contenido-->
    <main>
        <div class="container">
            <div class="row">
                <div class="col-md-6 order-md-1">
                    
                    <div id="carouselImages" class="carousel slide">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                
                                <img src="<?php echo $rutaImg ?>" class="d-block w-100">
                            </div>
                            <?php foreach($imagenes as $img) {?>
                            <div class="carousel-item">

                            <img src="<?php echo $img ?>" class="d-block w-100">
                            </div>
                            <?php } ?>
                            
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                        

                    
                </div>
                <div class="col-md-6 order-md-2">
                    <h2><?php echo $nombre ?></h2>

                    <?php if($descuento > 0) { ?>
                        <p><del><?php echo MONEDA . number_format($precio, 0,',','.');?><del><p>
                        <h2>
                            <?php echo MONEDA . number_format($precio_desc, 0,',','.');?>
                            <small class="text-success"><?php echo $descuento; ?>% descuento</small>
                        </h2>
                    <?php } else{ ?>

                    <h2><?php echo MONEDA . number_format($precio, 0,',','.');?></h2>

                    <?php } ?>

                    <p class="lead">
                        <?php echo $descripcion?>
                    </p>

                    <div class="d-grid gap-3 col-10 mx-auto">
                        <button class="btn btn-primary" type="button">Comprar ahora</button>
                        <button class="btn btn-outline-primary" type="button">Agregar al carrito</button>
                    </div>
                </div>
            </div>
        </div> 
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

</body>
</html>