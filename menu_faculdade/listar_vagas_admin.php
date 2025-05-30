<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

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

$id_faculdade = $_SESSION['id_faculdade'];

// Buscar nome da faculdade logada
$stmt = $conexao->prepare("SELECT nome FROM faculdade WHERE id_faculdade = ?");
$stmt->bind_param("i", $id_faculdade);
$stmt->execute();
$result_nome = $stmt->get_result();

if ($result_nome->num_rows === 0) {
    echo "<script>alert('Faculdade não encontrada!');</script>";
    exit;
}

$nome_faculdade = $result_nome->fetch_assoc()['nome'];

// Excluir vaga (se for da própria faculdade)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Verifica se a vaga pertence à faculdade logada antes de excluir
    $stmt_verifica = $conexao->prepare("SELECT id FROM vagas WHERE id = ? AND faculdade = ?");
    $stmt_verifica->bind_param("is", $id, $nome_faculdade);
    $stmt_verifica->execute();
    $res_verifica = $stmt_verifica->get_result();

    if ($res_verifica->num_rows > 0) {
        $stmt_delete = $conexao->prepare("DELETE FROM vagas WHERE id = ?");
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();
        echo "<script> alert('Vaga excluída com sucesso!'); </script>";
    } else {
        echo "<script> alert('Você não tem permissão para excluir esta vaga.'); </script>";
    }
}

// Buscar apenas as vagas da faculdade logada
$stmt_vagas = $conexao->prepare("SELECT * FROM vagas WHERE faculdade = ? ORDER BY data_postagem DESC");
$stmt_vagas->bind_param("s", $nome_faculdade);
$stmt_vagas->execute();
$result = $stmt_vagas->get_result();
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

