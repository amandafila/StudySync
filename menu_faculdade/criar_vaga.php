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

    $stmt = $conexao->prepare("INSERT INTO vagas (titulo, empresa, descricao, requisitos, localizacao) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $titulo, $empresa, $descricao, $requisitos, $localizacao);
    $stmt->execute();

    echo "Vaga criada com sucesso!";
}
?>

<form method="POST">
    <label>Título da vaga:</label><br>
    <input type="text" name="titulo" required><br>
    <label>Empresa:</label><br>
    <input type="text" name="empresa" required><br>
    <label>Descrição:</label><br>
    <textarea name="descricao" required></textarea><br>
    <label>Requisitos:</label><br>
    <textarea name="requisitos"></textarea><br>
    <label>Localização:</label><br>
    <input type="text" name="localizacao"><br><br>
    <button type="submit">Criar Vaga</button>
</form>