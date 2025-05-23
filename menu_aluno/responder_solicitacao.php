<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_solicitacao = $_POST['id_solicitacao'];
    $acao = $_POST['acao'];

    $novo_status = ($acao === 'aprovar') ? 'aprovado' : 'rejeitado';

    $sql = "UPDATE solicitacao_grupo SET status = '$novo_status' WHERE id_solicitacao = $id_solicitacao";
    $conexao->query($sql);

    if ($acao === 'aprovar') {
        $sqlInfo = "SELECT id_grupo, id_aluno FROM solicitacao_grupo WHERE id_solicitacao = $id_solicitacao";
        $resultInfo = $conexao->query($sqlInfo);
        if ($resultInfo && $resultInfo->num_rows > 0) {
            $dados = $resultInfo->fetch_assoc();
            $id_grupo = $dados['id_grupo'];
            $id_aluno = $dados['id_aluno'];

            $inserir = "INSERT INTO grupo_aluno (id_grupo, id_aluno, is_adm) VALUES ($id_grupo, $id_aluno, 0)";
            $conexao->query($inserir);
        }
    }

    header("Location: notificacoes.php");
    exit;
}
?>
