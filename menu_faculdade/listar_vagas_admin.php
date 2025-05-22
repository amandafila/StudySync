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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/listar_vagas_admin.css">
    <title>Vagas admin</title>
</head>
<body>
    <?php include('../header/header_facul.php'); ?>
    <div class="div_tabela">
        <table class="tabela">
            <tr class="tabela">
                <th class = "coluna">Título</th>
                <th>Empresa &nbsp;</th>
                <th>Localização &nbsp;</th>
                <th>Link de Inscrição &nbsp;</th>
                <th>Ações &nbsp;</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td class = "coluna"><?= htmlspecialchars($row['titulo']) ?></td>
                <td><?= htmlspecialchars($row['empresa']) ?></td>
                <td><?= htmlspecialchars($row['localizacao']) ?></td>
                <td>
                    <?php if (!empty($row['link'])): ?>
                        <a class="botao" href="<?= htmlspecialchars($row['link']) ?>" target="_blank">Inscrever-se</a>
                    <?php else: ?>
                        Sem link
                    <?php endif; ?>
                </td>
                <td class = "coluna"><a class="botao" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Excluir esta vaga?')">Excluir</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

