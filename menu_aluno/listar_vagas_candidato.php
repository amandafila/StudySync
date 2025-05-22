<?php
require_once("../conexao/conexao.php");
session_start();

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

$result = $conexao->query("SELECT * FROM vagas ORDER BY data_postagem DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/listar_vagas_aluno.css">
    <title>Listar vagas</title>
</head>
<body>
    <?php include('../header/header_aluno.php'); ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class = "div_vagas">
            <div class="div_vaga_interna">
                <h3><?= htmlspecialchars($row['titulo']) ?> – <?= htmlspecialchars($row['empresa']) ?></h3>
                <p class="paragrafo">Localização:&nbsp; <?= htmlspecialchars($row['localizacao']) ?></p>
                <p class="paragrafo">Descrição:&nbsp;<?= nl2br(htmlspecialchars($row['descricao'])) ?></p>
                <p class="paragrafo">Requisitos:&nbsp;<?= nl2br(htmlspecialchars($row['requisitos'])) ?></p>
                <p class="data_postagem paragrafo"><em>Postado em:&nbsp; <?= htmlspecialchars($row['data_postagem']) ?></em></p>
                <div class="div_link_inscricao">
                    <?php if (!empty($row['link'])): ?>
                        <p><a class="link_inscricao" href="<?= htmlspecialchars($row['link']) ?>" target="_blank">Inscrever-se</a></p>
                    <?php endif; ?>
                </div>
                </div>
        </div>
    <?php endwhile; ?>
</body>
</html>

