<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: ../login/login.php");
    exit;
}

if (!isset($_POST['id_grupo']) || !isset($_POST['titulo']) || !isset($_POST['mensagem']) || 
    empty($_POST['titulo']) || empty($_POST['mensagem'])) {
    header("Location: grupo_detalhes.php?id=" . ($_POST['id_grupo'] ?? ''));
    exit;
}

$id_grupo = $_POST['id_grupo'];
$id_aluno = $_SESSION['id_aluno'];
$titulo = $_POST['titulo'];
$mensagem = $_POST['mensagem'];

// Verificar se o aluno é admin do grupo
$sql_verifica = "SELECT 1 FROM grupo_aluno WHERE id_grupo = ? AND id_aluno = ? AND is_adm = 1";
$stmt = $conexao->prepare($sql_verifica);
$stmt->bind_param("ii", $id_grupo, $id_aluno);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    header("Location: grupo_detalhes.php?id=$id_grupo");
    exit;
}

// Inserir postagem
$sql_insert = "INSERT INTO forum_admins (id_grupo, id_aluno, titulo, mensagem) VALUES (?, ?, ?, ?)";
$stmt = $conexao->prepare($sql_insert);
$stmt->bind_param("iiss", $id_grupo, $id_aluno, $titulo, $mensagem);
$stmt->execute();

header("Location: grupo_detalhes.php?id=$id_grupo&aba=forumAdmins");
exit;
?>