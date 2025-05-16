<?php
require_once("../conexao/conexao.php");
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: ../login/login.php");
    exit;
}

if (!isset($_POST['id_grupo']) || !isset($_POST['mensagem']) || empty($_POST['mensagem'])) {
    header("Location: grupo_detalhes.php?id=" . ($_POST['id_grupo'] ?? ''));
    exit;
}

$id_grupo = $_POST['id_grupo'];
$id_aluno = $_SESSION['id_aluno'];
$mensagem = $_POST['mensagem'];

$sql_verifica = "SELECT 1 FROM grupo_aluno WHERE id_grupo = ? AND id_aluno = ?";
$stmt = $conexao->prepare($sql_verifica);
$stmt->bind_param("ii", $id_grupo, $id_aluno);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    header("Location: grupo_detalhes.php?id=$id_grupo");
    exit;
}

$sql_insert = "INSERT INTO forum_geral (id_grupo, id_aluno, mensagem) VALUES (?, ?, ?)";
$stmt = $conexao->prepare($sql_insert);
$stmt->bind_param("iis", $id_grupo, $id_aluno, $mensagem);
$stmt->execute();

header("Location: grupo_detalhes.php?id=$id_grupo&aba=forumGeral");
exit;
?>