<?php
require_once("../conexao/conexao.php");
session_start();

$erros = [];

if (!isset($_SESSION['id_faculdade'])) {
    echo "Erro: Não há uma faculdade associada à sua sessão. Verifique o login.";
    exit;
}

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'faculdade') {
    echo "<script>
        alert('Você não está logado!');
        window.location.href = '../login/login.php';
        </script>";
    exit; 
}

$id_faculdade = $_SESSION['id_faculdade'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $empresa = $_POST['empresa'];
    $descricao = $_POST['descricao'];
    $requisitos = $_POST['requisitos'];
    $localizacao = $_POST['localizacao'];
    $link = $_POST['link']; // Novo campo

    $stmt = $conexao->prepare("INSERT INTO vagas (titulo, empresa, descricao, requisitos, localizacao, link) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $titulo, $empresa, $descricao, $requisitos, $localizacao, $link);
    $stmt->execute();

    echo "<script>alert('Vaga criada com sucesso');</script>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/criar_vaga.css">
    <title>Criar Vagas</title>
</head>
<body>
    <form method="POST">
        <?php include('../header/header_facul.php'); ?>
        <div class="formulario">
            <div class="formulario_div">
                <label>Título da vaga:</label>
                <input class="campo" type="text" name="titulo" required>

                <label>Empresa:</label>
                <input class="campo" type="text" name="empresa" required>

                <label>Descrição:</label>
                <textarea class="campo" name="descricao" required></textarea>

                <label>Requisitos:</label>
                <textarea class="campo" name="requisitos"></textarea>

                <label>Localização:</label>
                <input class="campo" type="text" name="localizacao">

                <label>Link para inscrição:</label>
                <input class="campo" type="url" name="link" placeholder="https://exemplo.com" required>

                <button class="botao" type="submit">Criar Vaga</button>
            </div>
        </div>
    </form>
</body>
</html>
