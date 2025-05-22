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

<h2>Vagas Disponíveis</h2>
<?php while($row = $result->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
        <h3><?= $row['titulo'] ?> – <?= $row['empresa'] ?></h3>
        <p><strong>Localização:</strong> <?= $row['localizacao'] ?></p>
        <p><strong>Descrição:</strong><br><?= nl2br($row['descricao']) ?></p>
        <p><strong>Requisitos:</strong><br><?= nl2br($row['requisitos']) ?></p>
        <p><em>Postado em: <?= $row['data_postagem'] ?></em></p>
    </div>
<?php endwhile; ?>