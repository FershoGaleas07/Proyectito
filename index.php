<?php
require 'conexion.php';

$error="";
$hay_post = false;
$nombre = "";
$tipo_gasto = "";
$valor_gasto = "";
$codigoPersona = null;

$busqueda = "";
if(isset($_REQUEST['submit1'])){
    $hay_post = true;
    $nombre = isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : "";
    $tipo_gasto = isset($_REQUEST['cmbtipoGasto']) ? $_REQUEST['cmbtipoGasto'] : "";
    $valor_gasto = isset($_REQUEST['valor_gasto']) ? $_REQUEST['valor_gasto'] : "";


    if(!empty($nombre)){
        $nombre = preg_replace("/[^a-zA-ZáéíóúÁÉÍÓÚ]/u","",$nombre);
    }
    else{
        $error .= "El nombre no puede esta vácio<br>";
    }

    if($tipo_gasto==""){
        $error .= "Seleccione un gasto";
    }
    if($valor_gasto==""){
        $error .= "Seleccione un gasto";
    }

    if(!$error){
        $stm_insertarRegistro = $conexion->prepare("insert into gastos(nombre, tipo_gasto, valor_gasto) values(:nombre, :tipo_gasto, :valor_gasto)");
        $stm_insertarRegistro->execute([':nombre'=>$nombre, ':tipo_gasto'=>$tipo_gasto, ':valor_gasto'=>$valor_gasto]);
        header("Location: index.php?mensaje=registroGuardado");
        exit();
    }
}

if(isset($_REQUEST['submit2'])){
    $codigoPersona = $_REQUEST['id'];
    $nombre = isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : "";
    $tipo_gasto = isset($_REQUEST['cmbtipoGasto']) ? $_REQUEST['cmbtipoGasto'] : "";
    $valor_gasto = isset($_REQUEST['valor_gasto']) ? $_REQUEST['valor_gasto'] : "";

    if(!empty($nombre)){
        $nombre = preg_replace("/[^a-zA-ZáéíóúÁÉÍÓÚ]/u","",$nombre);
    }
    else{
        $error .= "El nombre no puede esta vácio<br>";
    }
    if($tipo_gasto==""){
        $error .= "Seleccione un tipo de gasto";
    }
    if($valor_gasto==""){
        $error .= "Introduzca un valor de gasto";
    }

    if(!$error){
        $stm_modificar = $conexion->prepare("update gastos set nombre = :nombre, tipo_gasto = :tipo_gasto, valor_gasto = :valor_gasto where codigoPersona = :id");
        $stm_modificar->execute([
            ':nombre'=>$nombre,
            ':tipo_gasto'=>$tipo_gasto,
            ':valor_gasto'=>$valor_gasto,
            ':id'=> $codigoPersona
        ]);
        header("Location: index.php?mensaje=registroModificado");
        exit();
    }
}


if(isset($_REQUEST['id']) && isset($_REQUEST['op'])){
    $id = $_REQUEST['id'];
    $op = $_REQUEST['op'];

    if($op == 'm'){
        // $stm_seleccionarRegistro = $conexion->prepare("update cliente set nombreUsuario=:nombre, sexo=:sexo, pais:pais");
        $stm_seleccionarRegistro = $conexion->prepare("select * from gastos where codigoPersona=:id");
        $stm_seleccionarRegistro->execute([':id'=>$id]);
        $resultado = $stm_seleccionarRegistro->fetch();
        $codigoPersona = $resultado['codigoPersona'];
        $nombre = $resultado['nombre'];
        $tipo_gasto = $resultado['tipo_gasto'];
        $valor_gasto = $resultado['valor_gasto'];
    }
    else if($op == 'e'){
        $stm_eliminar = $conexion->prepare("delete from gastos where codigoPersona = :id");
        $stm_eliminar->execute([':id'=>$id]);
        header("Location: index.php?mensaje=registroEliminado");
        exit();
    }
}


$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if (!empty($busqueda)) {
    $stm = $conexion->prepare("SELECT * FROM gastos WHERE nombre LIKE :nombre");
    $stm->execute([':nombre' => "%$busqueda%"]);
} else {
    $stm = $conexion->prepare("SELECT * FROM gastos");
    $stm->execute([]);
}

$resultados = $stm->fetchAll();


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gatos Familiares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body style="background-image: url('https://img.freepik.com/vector-premium/fondo-pantalla-fondo-concepto-negocio-patrones-fisuras-finanzas-o-economia_78677-9978.jpg')">




<div class="position-absolute top-50 start-50 translate-middle z-0 position-absolute p-5 rounded-3 shadow-lg p-3 mb-5 bg-body-tertiary rounded">
    <h1 class="text-center">GASTOS FAMILIARES</h1>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="hidden" name="id" value="<?php echo isset($codigoPersona)? $codigoPersona : "" ?>">
        <label class="form-label" for="nombre">Nombre Completo:</label>
        <input class="form-control" type="text" name="nombre" id="nombre" value="<?php echo isset($nombre)? $nombre : "" ?>"><br>


        <label class="form-label" for="tipo_gasto">Tipo Gasto: </label>
        <select class="form-select" name="cmbtipoGasto" id="tipo_gasto">
            <option value="">Seleccione un tipo de gasto: </option>
            <option value="Alimentación" <?php echo ($tipo_gasto=='Alimentación')? 'selected' : '' ?> >Alimentación</option>
            <option value="Transporte" <?php echo ($tipo_gasto=='Transporte')? 'selected' : '' ?>>Transporte</option>
            <option value="Salud" <?php echo  ($tipo_gasto=='Salud')? 'selected' : '' ?>>Salud</option>
            <option value="Luz" <?php echo  ($tipo_gasto=='Luz')? 'selected' : '' ?>>Luz</option>
            <option value="Hormiga" <?php echo  ($tipo_gasto=='Hormiga')? 'selected' : '' ?>>Hormiga</option>
        </select><br>


        <label class="form-label" for="valor_gasto">Ingrese valor de gasto:</label>
        <input class="form-control" type="text" name="valor_gasto" id="valor_gasto" value="<?php echo isset($valor_gasto)? $valor_gasto : "" ?>"><br>

        <input class="btn btn-primary" type="submit" value="Enviar" name="submit1">

        <?php
        if($codigoPersona){
            echo '<input class="btn btn-dark" type="submit" value="Modificar" name="submit2">';
        }
        ?>
        <a class="btn btn-secondary" href="index.php">Cancelar</a>
    </form>
    <br>
    <?php if($error):  ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo "<p>$error</p>"; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>


    <?php
    if(isset($_REQUEST['mensaje'])){
        $mensaje = $_REQUEST['mensaje'];
        ?>
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            <?php
            if($mensaje=='registroGuardado'){
                echo "<p>Registro guardado.</p>";
            }
            elseif($mensaje == 'registroModificado'){
                echo "<p>Registro modificado.</p>";
            }
            elseif($mensaje=='registroEliminado'){
                echo "<p>Registro eliminado.</p>";
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
    }
    ?>

    <form method="GET" action="<?php echo $_SERVER['PHP_SELF'] ?>" class="mb-3">
        <input type="text" name="buscar" class="form-control w-50 d-inline" placeholder="Buscar por nombre..." value="<?php echo htmlspecialchars($busqueda); ?>">
        <input type="submit" class="btn btn-success ms-2" value="Buscar">
    </form>
    <table class="table table-bordered table-hover">
        <thead>
        <th>Nombre</th>
        <th>Tipo Gasto</th>
        <th>Valor Gasto</th>
        <th colspan="2">Acciones</th>
        </thead>
        <tbody>
        <?php foreach($resultados as $registro): ?>
        <tr>
            <td><?php echo $registro['nombre']; ?></td>
            <td><?php echo $registro['tipo_gasto']; ?></td>
            <td><?php echo $registro['valor_gasto']; ?></td>
            <td><a class="btn btn-primary" href="index.php?id=<?php echo $registro['codigoPersona'] ?>&op=m">Modificar</a></td>
            <td><a class="btn btn-danger" href="index.php?id=<?php echo $registro['codigoPersona'] ?>&op=e" onclick="return confirm('Desea eliminar el registro');">Eliminar</a></td>
            <?php endforeach; ?>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
