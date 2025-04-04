<?php

    $conexao = new mysqli($server, $user, $pass, $db_name);

    if ($conexao->connect_error) {
        die("Erro de conexão: " . $conexao->connect_error);
    }
?>
