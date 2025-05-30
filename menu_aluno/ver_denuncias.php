<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['id_aluno'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_aluno = $_SESSION['id_aluno'];

$sql_check_admin = "SELECT COUNT(*) AS is_admin 
                   FROM grupo_aluno 
                   WHERE id_aluno = ? AND is_adm = 1";
$stmt = $conexao->prepare($sql_check_admin);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result_check = $stmt->get_result();
$is_admin = $result_check->fetch_assoc()['is_admin'] > 0;

if (!$is_admin) {
    echo "<script>alert('Acesso restrito a administradores.'); window.location.href='grupo_detalhes.php';</script>";
    exit;
}

$sql = "SELECT 
    d.id_denuncia, 
    d.data_hora, 
    d.tipo_forum, 
    a.nome AS nome_aluno,
    g.nome AS nome_grupo,
    g.id_grupo,
    CASE 
        WHEN d.tipo_forum = 'geral' THEN fg.mensagem
        WHEN d.tipo_forum = 'admins' THEN fa.mensagem
        ELSE 'Post não encontrado'
    END AS mensagem_post
FROM denuncias d
JOIN aluno a ON d.id_aluno = a.id_aluno
LEFT JOIN forum_geral fg ON (d.tipo_forum = 'geral' AND d.id_post = fg.id_post)
LEFT JOIN forum_admins fa ON (d.tipo_forum = 'admins' AND d.id_post = fa.id_post)
LEFT JOIN grupo g ON (
    (d.tipo_forum = 'geral' AND g.id_grupo = fg.id_grupo) OR
    (d.tipo_forum = 'admins' AND g.id_grupo = fa.id_grupo)
)
WHERE g.id_grupo IN (
    SELECT id_grupo 
    FROM grupo_aluno 
    WHERE id_aluno = ? AND is_adm = 1
)
ORDER BY d.data_hora DESC";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Erro na consulta: " . $conexao->error);
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Denúncias Recebidas - Admin</title>
    <link rel="stylesheet" href="../assets/styles/ver_denunciass.css">
</head>
<body>
    <?php include('../header/header_aluno.php'); ?>

    <h2>Denúncias Recebidas</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="caixa_not">
                <div class="info">
                    <p><strong>ID da Denúncia:</strong> <?php echo htmlspecialchars($row['id_denuncia']); ?></p>
                    
                    <div class="mensagem-denuncia">
                        <p><strong>Mensagem do Post:</strong> <?php echo nl2br(htmlspecialchars($row['mensagem_post'])); ?></p>
                    </div>

                    <div class="mensagem-info">
                        <p><strong>Grupo:</strong> <?php echo htmlspecialchars($row['nome_grupo']); ?></p>
                        <p><strong>ID do Grupo:</strong> <?php echo !empty($row['id_grupo']) ? htmlspecialchars($row['id_grupo']) : 'N/A'; ?></p>
                    </div>
                    
                    <p><strong>Tipo do Fórum:</strong> <?php echo htmlspecialchars($row['tipo_forum']); ?></p>
                    <p><strong>Aluno que denunciou:</strong> <?php echo htmlspecialchars($row['nome_aluno']); ?></p>
                    <p><strong>Data da Denúncia:</strong> <?php echo date('d/m/Y H:i', strtotime($row['data_hora'])); ?></p>
                    
                    <div class="botao-excluir">
                        <form action="excluir_mensagem.php" method="post">
                            <input type="hidden" name="id_denuncia" value="<?php echo htmlspecialchars($row['id_denuncia']); ?>">
                            <input type="hidden" name="tipo_forum" value="<?php echo htmlspecialchars($row['tipo_forum']); ?>">
                            <input type="hidden" name="id_grupo" value="<?php echo !empty($row['id_grupo']) ? htmlspecialchars($row['id_grupo']) : ''; ?>">
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