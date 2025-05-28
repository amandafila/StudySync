<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: ../login/login.php");
    exit;
}

if (!isset($_POST['id_post']) || !isset($_POST['tipo_forum']) || !isset($_POST['id_grupo'])) {
    header("Location: meus_grupos.php");
    exit;
}

$id_post = $_POST['id_post'];
$tipo_forum = $_POST['tipo_forum'];
$id_grupo = $_POST['id_grupo'];
$id_aluno = $_SESSION['id_aluno'];

// Verificar se o usuário tem permissão para excluir (é admin ou autor do post)
$sql_verifica = "SELECT ga.is_adm, fg.id_aluno 
                 FROM grupo_aluno ga
                 LEFT JOIN forum_geral fg ON fg.id_post = ? AND fg.id_grupo = ga.id_grupo
                 WHERE ga.id_grupo = ? AND ga.id_aluno = ?";
if ($tipo_forum == 'admins') {
    $sql_verifica = "SELECT ga.is_adm, fa.id_aluno 
                     FROM grupo_aluno ga
                     LEFT JOIN forum_admins fa ON fa.id_post = ? AND fa.id_grupo = ga.id_grupo
                     WHERE ga.id_grupo = ? AND ga.id_aluno = ?";
}

$stmt = $conexao->prepare($sql_verifica);
$stmt->bind_param("iii", $id_post, $id_grupo, $id_aluno);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    // Usuário não tem permissão
    header("Location: grupo_detalhes.php?id=" . $id_grupo);
    exit;
}

$dados = $resultado->fetch_assoc();

if (!$dados['is_adm'] && $dados['id_aluno'] != $id_aluno) {
    // Usuário não é admin nem autor do post
    header("Location: grupo_detalhes.php?id=" . $id_grupo);
    exit;
}

// Se chegou aqui, pode excluir
if ($tipo_forum == 'geral') {
    $sql_excluir = "DELETE FROM forum_geral WHERE id_post = ?";
} else {
    $sql_excluir = "DELETE FROM forum_admins WHERE id_post = ?";
}

$stmt = $conexao->prepare($sql_excluir);
$stmt->bind_param("i", $id_post);
$stmt->execute();

header("Location: grupo_detalhes.php?id=" . $id_grupo);
exit;
?>