<?php
require_once("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_faculdade = $_POST['id_faculdade'];
    $id_usuario = $_POST['id_usuario'];
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $cnpj = $_POST['cnpj'];

    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }


    $stmt_usuario = $conexao->prepare("UPDATE usuario SET username = ?, email = ? WHERE id = ?");
    $stmt_usuario->bind_param("ssi", $usuario, $email, $id_usuario);
    $stmt_usuario->execute();
    $stmt_usuario->close();


    $stmt_faculdade = $conexao->prepare("UPDATE faculdade SET cnpj = ? WHERE id_faculdade = ?");
    $stmt_faculdade->bind_param("si", $cnpj, $id_faculdade);
    $stmt_faculdade->execute();
    $stmt_faculdade->close();

    $conexao->close();

    header("Location: index.php?sucesso=atualizado");
    exit();
}
?>
