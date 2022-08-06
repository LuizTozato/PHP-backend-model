<?php

    //CONFIGURAÇÕES GERAIS
    $servidor = "localhost";
    $usuario = "root";
    $senha = "";
    $banco = "banco_newm";

    //CONEXÃO
    $pdo = new PDO(
        "mysql:host=$servidor;dbname=$banco",
        $usuario,
        $senha
    );
?>