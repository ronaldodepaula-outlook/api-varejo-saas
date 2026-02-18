<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
/*******************************************
**  CLASSE DE INCLUSÃO DE PÁGINAS
**  MÉTODO - trocarURL($url)
**  ESTA CLASSE FAZ A TROCA DE PÁGINAS NA INDEX
**  VERSÃO 1.1
********************************************/
class verURL {
    function trocarURL($url) {
        if (empty($url)) {
            $url = "view/home.php"; // Página padrão se $url estiver vazia
        } else {
            $url = "view/$url.php"; // Monta o caminho da página
        }

        // Verifica se a página existe
        if (file_exists($url)) {
            include_once($url); // Inclui a página se existir
        } else {
            // Se a página não existir, inclui a página de erro 404
            $this->showErrorPage(404); // Chama o método para mostrar a página de erro
        }
    }

    // Método para mostrar páginas de erro
    private function showErrorPage($errorCode) {
        switch ($errorCode) {
            case 404:
                include_once("view/error/404.php"); // Página de erro 404
                break;
            case 500:
                include_once("view/error/500.php"); // Página de erro 500
                break;
            default:
                include_once("view/error/default.php"); // Página de erro padrão
                break;
        }
    }
}
?>
