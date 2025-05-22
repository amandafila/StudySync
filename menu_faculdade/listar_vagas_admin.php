<?php
require_once("../conexao/conexao.php");

session_start();

$erros = [];

if (!isset($_SESSION['id_faculdade'])) {
    echo "Erro: Não há uma faculdade associada à sua sessão. Verifique o login.";
    exit;
}

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'faculdade') {
    echo "<script>
        alert('Você não está logado!');
        window.location.href = '../login/login.php';
        </script>";
    exit; 
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conexao->query("DELETE FROM vagas WHERE id = $id");
    echo "Vaga excluída com sucesso!";
}

$result = $conexao->query("SELECT * FROM vagas ORDER BY data_postagem DESC");
?>

<h2>Vagas de Trabalho</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Título</th>
        <th>Empresa</th>
        <th>Localização</th>
        <th>Ações</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['titulo'] ?></td>
        <td><?= $row['empresa'] ?></td>
        <td><?= $row['localizacao'] ?></td>
        <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Excluir esta vaga?')">Excluir</a></td>
    </tr>
    <?php endwhile; ?>
</table>