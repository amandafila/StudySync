<?php
require_once("../conexao/conexao.php");

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