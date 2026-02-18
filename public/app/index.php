<?php
session_start();
if (!isset($_SESSION['authToken'], $_SESSION['usuario'], $_SESSION['empresa'], $_SESSION['licenca'])) {
    header('Location: login.php');
    exit;
}
$usuario = $_SESSION['usuario'];
$empresa = $_SESSION['empresa'];
$licenca = $_SESSION['licenca'];
$token = $_SESSION['authToken'];
$segmento = $_SESSION['segmento'];

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include 'components/header.php'; ?>
</head>
<body>
    <div class="main-wrapper">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>
        
        <!-- Conteúdo Principal -->
         
                <?php
					include_once("classe/verURL.php");
					$url = new verURL();
					$url->trocarURL($_GET["view"]);
                    //echo "teste".$cod_user.$strUsrLevel;
          		?>
    </div>
    
    
</body>
</html>
