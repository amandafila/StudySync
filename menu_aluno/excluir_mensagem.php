<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_denuncia = $_POST['id_denuncia'] ?? null;
    $tipo_forum = $_POST['tipo_forum'] ?? null;

    if ($id_denuncia && $tipo_forum) {

        $stmt = $conexao->prepare("SELECT id_post FROM denuncias WHERE id_denuncia = ?");
        $stmt->bind_param("i", $id_denuncia);
        $stmt->execute();
        $stmt->bind_result($id_post);
        $stmt->fetch();
        $stmt->close();

        if (!empty($id_post)) {
           
            if ($tipo_forum === 'geral') {
                $stmt = $conexao->prepare("DELETE FROM forum_geral WHERE id_post = ?");
            } elseif ($tipo_forum === 'admins') {
                $stmt = $conexao->prepare("DELETE FROM forum_admins WHERE id_post = ?");
            }

            if (isset($stmt)) {
                $stmt->bind_param("i", $id_post);
                $stmt->execute();
                $stmt->close();
            }
        }

    
        $stmt = $conexao->prepare("DELETE FROM denuncias WHERE id_denuncia = ?");
        $stmt->bind_param("i", $id_denuncia);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: ver_denuncias.php?msg=excluida");
exit;
?>
