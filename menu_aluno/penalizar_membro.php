<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_grupo'], $_POST['id_aluno'])) {
    $id_grupo = $_POST['id_grupo'];
    $id_aluno = $_POST['id_aluno'];

    $sql = "UPDATE grupo_aluno 
            SET penalizado = 1, data_penalizacao = NOW()
            WHERE id_grupo = ? AND id_aluno = ?";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $id_grupo, $id_aluno);

    if ($stmt->execute()) {
        header("Location: grupo_detalhes.php?id=$id_grupo");
        exit;
    } else {
        echo "Erro ao penalizar aluno.";
    }

    $stmt->close();
} else {
    header("Location: meus_grupos.php");
    exit;
}
?>
