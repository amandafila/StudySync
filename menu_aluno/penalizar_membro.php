<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: ../login/login.php");
    exit;
}

if (!isset($_POST['id_grupo']) || !isset($_POST['id_aluno'])) {
    header("Location: meus_grupos.php");
    exit;
}

$id_grupo = $_POST['id_grupo'];
$id_aluno_penalizado = $_POST['id_aluno'];
$id_adm = $_SESSION['id_aluno'];

// Verificar se o usuário é admin do grupo
$sql_verifica_adm = "SELECT is_adm FROM grupo_aluno 
                    WHERE id_grupo = ? AND id_aluno = ? AND is_adm = 1";
$stmt = $conexao->prepare($sql_verifica_adm);
$stmt->bind_param("ii", $id_grupo, $id_adm);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: grupo_detalhes.php?id=" . $id_grupo);
    exit;
}


$sql_status = "SELECT penalizado FROM grupo_aluno 
              WHERE id_grupo = ? AND id_aluno = ?";
$stmt = $conexao->prepare($sql_status);
$stmt->bind_param("ii", $id_grupo, $id_aluno_penalizado);
$stmt->execute();
$resultado = $stmt->get_result();
$dados = $resultado->fetch_assoc();

$novo_status = ($dados['penalizado'] == 1) ? 0 : 1;


$sql_penalizar = "UPDATE grupo_aluno 
                 SET penalizado = ?, data_penalizacao = NOW() 
                 WHERE id_grupo = ? AND id_aluno = ?";
$stmt = $conexao->prepare($sql_penalizar);
$stmt->bind_param("iii", $novo_status, $id_grupo, $id_aluno_penalizado);
$stmt->execute();

header("Location: grupo_detalhes.php?id=" . $id_grupo);
exit;
?>