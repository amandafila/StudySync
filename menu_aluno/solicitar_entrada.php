<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

$erros = [];

if (!isset($_SESSION['id_aluno'])) {
    echo "Erro: Não há um aluno associado à sua sessão. Verifique o login.";
    exit;
}

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    echo "<script>
        alert('Você não está logado!');
        window.location.href = '../login/login.php';
    </script>";
    exit;
}

$id_aluno = $_SESSION['id_aluno'];

$sql_grupos = "
    SELECT g.id_grupo, g.nome
    FROM grupo g
    WHERE g.id_grupo NOT IN (
        SELECT ga.id_grupo
        FROM grupo_aluno ga
        WHERE ga.id_aluno = $id_aluno
    )
";
$grupos_disponiveis = $conexao->query($sql_grupos);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_grupo = intval($_POST['id_grupo']);
    $mensagem = $conexao->real_escape_string($_POST['mensagem']);

    $checkSql = "SELECT * FROM solicitacao_grupo 
                 WHERE id_grupo = $id_grupo AND id_aluno = $id_aluno AND status = 'pendente'";
    $checkResult = $conexao->query($checkSql);

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Você já solicitou entrada neste grupo. Aguarde aprovação.');</script>";
    } else {
        $insertSql = "INSERT INTO solicitacao_grupo (id_grupo, id_aluno, mensagem) 
                      VALUES ($id_grupo, $id_aluno, '$mensagem')";
        if ($conexao->query($insertSql)) {
            echo "<script>alert('Solicitação enviada com sucesso!'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Erro ao enviar solicitação.');</script>";
        }
    }
}
$queryFaculdade = "SELECT f.id_faculdade
                   FROM aluno a
                   JOIN faculdade f ON a.faculdade = f.nome
                   WHERE a.id_aluno = $id_aluno";
$resFaculdade = $conexao->query($queryFaculdade);

if ($resFaculdade && $resFaculdade->num_rows > 0) {
    $id_faculdade = $resFaculdade->fetch_assoc()['id_faculdade'];
} else {
    echo "Erro: não foi possível identificar a faculdade do aluno.";
    exit;
}
$sql_grupos = "
    SELECT g.id_grupo, g.nome
    FROM grupo g
    WHERE g.id_faculdade = $id_faculdade
      AND g.id_grupo NOT IN (
        SELECT ga.id_grupo
        FROM grupo_aluno ga
        WHERE ga.id_aluno = $id_aluno
    )
";
$grupos_disponiveis = $conexao->query($sql_grupos);


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/styles/solicitacoes.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitação de Grupo</title>
</head>
<body>
    <?php include('../header/header_aluno.php'); ?>

    <div class="container-form">
        <h1>Solicitar entrada em grupo</h1>
        <form class="form-container" action="" method="POST">
            <label for="id_grupo">Selecione um grupo:</label>
            <select name="id_grupo" id="id_grupo" required>
                <option value="" disabled selected>Selecione um grupo</option>
                <?php
                    if ($grupos_disponiveis && $grupos_disponiveis->num_rows > 0) {
                        while ($grupo = $grupos_disponiveis->fetch_assoc()) {
                            echo "<option value='{$grupo['id_grupo']}'>{$grupo['nome']}</option>";
                        }
                    } else {
                        echo "<option value='' disabled>Nenhum grupo disponível</option>";
                    }
                ?>
            </select>

            <label for="mensagem">Mensagem:</label>
            <textarea name="mensagem" id="mensagem" placeholder="Digite uma mensagem" required></textarea> <br>

            <button type="submit">Solicitar</button>
        </form>
    </div>
</body>
</html>