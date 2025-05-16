<?php
require_once("../conexao/conexao.php");
session_start();

// Verificar se o usuário está logado como aluno
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: ../login/login.php");
    exit;
}

// Verificar se todos os parâmetros foram enviados
if (!isset($_POST['id_grupo']) || !isset($_POST['id_aluno'])) {
    header("Location: meus_grupos.php");
    exit;
}

$id_grupo = $_POST['id_grupo'];
$id_aluno_remover = $_POST['id_aluno'];
$id_aluno_sessao = $_SESSION['id_aluno'];

// Verificar se o aluno que está tentando remover é administrador do grupo
$sql_verifica_adm = "SELECT is_adm FROM grupo_aluno 
                     WHERE id_grupo = ? AND id_aluno = ?";
$stmt = $conexao->prepare($sql_verifica_adm);
$stmt->bind_param("ii", $id_grupo, $id_aluno_sessao);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0 || !$resultado->fetch_assoc()['is_adm']) {
    // Não é administrador
    header("Location: grupo_detalhes.php?id=" . $id_grupo);
    exit;
}

// Remover o membro do grupo
$sql_remover = "DELETE FROM grupo_aluno 
                WHERE id_grupo = ? AND id_aluno = ?";
$stmt = $conexao->prepare($sql_remover);
$stmt->bind_param("ii", $id_grupo, $id_aluno_remover);
$stmt->execute();

header("Location: grupo_detalhes.php?id=" . $id_grupo . "&removido=1");
exit;