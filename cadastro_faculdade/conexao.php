<?php
$servidor = "127.0.0.1";
$usuario = "root";
$senha = "Vi28112712";
$banco = "studysync";

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

// Verificar conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}
?>
