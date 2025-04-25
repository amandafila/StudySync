<?php
require_once("../conexao/conexao.php");
session_start();

$erros = [];

if (!isset($_SESSION['id_aluno'])) {
    echo "Erro: Não há um aluno associado à sua sessão. Verifique o login.";
    exit;
}

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    echo "<script>
        alert('Você não está logado!');
        window.location.href = '../login/login.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Aluno</title>
    <link rel="stylesheet" href="../assets/styles/menu_aluno.css">
</head>
<body>
    <header class="cabecalho">
        <h1>StudySnic</h1>
    </header>

    <div class="geral">
        <a class='quadrado' href="solicitar_entrada.php">Solicitar entrada em grupo</a> <br>
        <a class='quadrado' href="notificacoes.php">Notificações</a> <br>
        <a class='quadrado' href="meus_grupos.php">Meus grupos</a> <br>
    </div>
</body>
</html>
