<?php
require_once("conexao.php");

// Verifica conexão
if ($conexao->connect_error) {
  die("Erro na conexão: " . $conexao->connect_error);
}

// Verifica se veio o ID do usuário
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Primeiro exclui da tabela faculdade (dependente)
    $stmt_facul = $conexao->prepare("DELETE FROM faculdade WHERE id_usuario = ?");
    $stmt_facul->bind_param("i", $id);
    $stmt_facul->execute();
    $stmt_facul->close();

   
    $stmt_usuario = $conexao->prepare("DELETE FROM usuario WHERE id = ?");
    $stmt_usuario->bind_param("i", $id);
    $stmt_usuario->execute();

    if ($stmt_usuario->affected_rows > 0) {
        echo "<script>alert('Usuário excluído com sucesso!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir o usuário.'); window.location.href='index.php';</script>";
    }

    $stmt_usuario->close();
    $conexao->close();
} else {
    echo "<script>alert('ID não fornecido!'); window.location.href='index.php';</script>";
}
?>
