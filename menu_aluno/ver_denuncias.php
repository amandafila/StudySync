<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['id_aluno'])) {
    echo "Erro: Faça login.";
    exit;
}

$id_admin = $_SESSION['id_aluno'];

$sql = "
SELECT d.id_denuncia, d.data_hora, d.tipo_forum, a.nome AS nome_aluno,
       CASE 
         WHEN d.tipo_forum = 'geral' THEN (SELECT mensagem FROM forum_geral WHERE id_post = d.id_post)
         WHEN d.tipo_forum = 'admins' THEN (SELECT mensagem FROM forum_admins WHERE id_post = d.id_post)
         ELSE 'Post não encontrado'
       END AS mensagem_post
FROM denuncias d
JOIN aluno a ON d.id_aluno = a.id_aluno
ORDER BY d.data_hora DESC";

$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Denúncias Recebidas - Admin</title>
    <link rel="stylesheet" href="../assets/styles/ver_denuncias.css">
</head>
<body>
    <?php include('../header/header_aluno.php'); ?>

    <h2>Denúncias Recebidas</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="caixa_not">
    <div class="info">
        <p><strong>ID da Denúncia:</strong> <?php echo $row['id_denuncia']; ?></p>
        
        <div class="mensagem-denuncia">
            <p><strong>Mensagem do Post:</strong> <?php echo nl2br(htmlspecialchars($row['mensagem_post'])); ?></p>
        </div>

        
        <p><strong>Tipo do Fórum:</strong> <?php echo htmlspecialchars($row['tipo_forum']); ?></p>
        <p><strong>Aluno que denunciou:</strong> <?php echo htmlspecialchars($row['nome_aluno']); ?></p>
        <p><strong>Data da Denúncia:</strong> <?php echo date('d/m/Y H:i', strtotime($row['data_hora'])); ?></p>
        <div class="botao-excluir">
            <form action="excluir_mensagem.php" method="post">
                <input type="hidden" name="id_denuncia" value="<?php echo $row['id_denuncia']; ?>">
                <input type="hidden" name="tipo_forum" value="<?php echo $row['tipo_forum']; ?>">
                <button type="submit">Excluir mensagem</button>
            </form>
        </div>
    </div>
</div>

        <?php endwhile; ?>
    <?php else: ?>
        <p class="nao_tem">Nenhuma denúncia registrada.</p>
    <?php endif; ?>
</body>
</html>
