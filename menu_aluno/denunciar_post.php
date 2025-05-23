<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: ../login/login.php");
    exit;
}

if (isset($_POST['id_post']) && isset($_POST['tipo_forum'])) {
    $id_post = $_POST['id_post'];
    $tipo_forum = $_POST['tipo_forum'];
    $id_aluno = $_SESSION['id_aluno'];

    $sql = "INSERT INTO denuncias (id_post, id_aluno, tipo_forum) VALUES (?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iis", $id_post, $id_aluno, $tipo_forum);

    if ($stmt->execute()) {
        echo "Denúncia registrada com sucesso.";
    } else {
        echo "Erro ao registrar denúncia.";
    }
} else {
    echo "Dados inválidos.";
}
?>
 