<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_grupo'])) {
    $idGrupo = intval($_POST['id_grupo']);

    $conexao->query("DELETE FROM grupo_aluno WHERE id_grupo = $idGrupo");

    $sql = "DELETE FROM grupo WHERE id_grupo = $idGrupo";
    if ($conexao->query($sql)) {
        echo "<script>
                alert('Grupo excluído com sucesso!');
                window.location.href = 'visualizar_grupos.php';
            </script>";
    } else {
        echo "Erro ao excluir o grupo: " . $conexao->error;
    }

    $conexao->close();
} else {
    echo "ID do grupo não foi informado.";
}
?>
