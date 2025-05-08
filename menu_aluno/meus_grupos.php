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
    exit;}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/meus_grupos.css">
    <title>Document</title>
</head>
<body>
    <header> 
        <a class="esquerda" href="../menu/index.html">StudySync</a> 
        <a href="menu_aluno.php" class="menu_aluno">menu</a>
        <div class="identificador">Olá, aluno</div>
    </header>
</body>
</html>