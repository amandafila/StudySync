<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: ../login/login.php");
    exit;
}

if (!isset($_GET['id_grupo'])) {
    header("Location: meus_grupos.php");
    exit;
}

$id_grupo = $_GET['id_grupo'];
$id_aluno = $_SESSION['id_aluno'];

$sql_verifica = "SELECT 1 FROM grupo_aluno WHERE id_grupo = ? AND id_aluno = ?";
$stmt = $conexao->prepare($sql_verifica);
$stmt->bind_param("ii", $id_grupo, $id_aluno);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    header("Location: meus_grupos.php");
    exit;
}

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

$sql_grupo = "SELECT nome FROM grupo WHERE id_grupo = ?";
$stmt = $conexao->prepare($sql_grupo);
$stmt->bind_param("i", $id_grupo);
$stmt->execute();
$nome_grupo = $stmt->get_result()->fetch_assoc()['nome'];

$sql_count = "SELECT COUNT(*) as total FROM forum_geral WHERE id_grupo = ?";
$stmt = $conexao->prepare($sql_count);
$stmt->bind_param("i", $id_grupo);
$stmt->execute();
$total_posts = $stmt->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total_posts / $por_pagina);

$sql_posts = "SELECT fg.*, a.nome as autor 
              FROM forum_geral fg
              JOIN aluno a ON fg.id_aluno = a.id_aluno
              WHERE fg.id_grupo = ?
              ORDER BY fg.data_postagem DESC
              LIMIT ?, ?";
$stmt = $conexao->prepare($sql_posts);
$stmt->bind_param("iii", $id_grupo, $offset, $por_pagina);
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fórum Geral - <?php echo htmlspecialchars($nome_grupo); ?> | StudySync</title>
    <link rel="stylesheet" href="../assets/styles/forum_geral_completo.css">
</head>
<body>
    <?php include('../header/header_aluno.php'); ?>

    <main>
        <div class="forum-header">
            <h1>Fórum Geral - <?php echo htmlspecialchars($nome_grupo); ?></h1>
            <a href="grupo_detalhes.php?id=<?php echo $id_grupo; ?>" class="btn">Voltar para o Grupo</a>
        </div>

        <div class="forum-content">
            <a href="#post-form" class="new-post-btn">Nova Postagem</a>

            <div class="posts-container">
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post">
                            <div class="post-header">
                                <span class="post-author"><?php echo htmlspecialchars($post['autor']); ?></span>
                                <span class="post-date"><?php echo date('d/m/Y H:i', strtotime($post['data_postagem'])); ?></span>
                            </div>
                            <div class="post-content">
                                <?php echo nl2br(htmlspecialchars($post['mensagem'])); ?>
                            </div>
                            <form action="denunciar_post.php" method="post" class="denunciar-form" style="margin-top: 5px;">
                                <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                                <input type="hidden" name="tipo_forum" value="geral">
                                <button type="submit" class="btn-denunciar">Denunciar</button>
                            </form>

                        </div>
                    <?php endforeach; ?>

                    <div class="pagination">
                        <?php if ($pagina > 1): ?>
                            <a href="?id_grupo=<?php echo $id_grupo; ?>&pagina=<?php echo $pagina-1; ?>">Anterior</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <?php if ($i == $pagina): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?id_grupo=<?php echo $id_grupo; ?>&pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($pagina < $total_paginas): ?>
                            <a href="?id_grupo=<?php echo $id_grupo; ?>&pagina=<?php echo $pagina+1; ?>">Próxima</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p>Nenhuma postagem ainda no fórum geral.</p>
                <?php endif; ?>
            </div>

            <div id="post-form" class="post-form">
                <h2>Criar Nova Postagem</h2>
                <form action="postar_forum_geral.php" method="post">
                    <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>">
                    <textarea name="mensagem" placeholder="Escreva sua mensagem..." required></textarea>
                    <button type="submit" class="btn">Postar Mensagem</button>
                </form>
            </div>
        </div>
    </main>
    <script>
        const palavrasProibidas = [
            "lazarento",
        "arrombado",
        "foda",
        "foda-se",
        "vai se foder",
        "vai a merda",
        "arrombada",
        "fudeo",
        "fudeu",
        "gozar",
        "gozada",
        "merda",
        "bosta",
        "punheta",
        "filho da puta",
        "filha da puta",
        "punhetinha",
        "punheteiro",
        "fdp",
        "cuzão",
        "cusão",
        "cusão",
        "viado",
        "viadinho",
        "xota",
        "putaria",
        "putero",
        "puto",
        "puteiro",
        "bilau",
        "vadiazinha",
        "putinha",
        "babaca",
        "retardado",
        "cusinho",
        "cuzinho",
        "filho de um corno",
        "rapariga",
        "rabão",
        "vadia",
        "puta",
        "arrombada",
        "caralho",
        "broxa",
        "imbecil",
        "imbessil",
        "bastarda",
        "bastardo",
        "buceta",
        "bucetuda",
        "vsf",
        "vai se foder",
        "boceta",
        "cu",
        "rola",
        "rolas",
        "biscate",
        "pau no cu",
        "pau no seu cu",
        "pica",
        "pika",
        "piroca",
        "piroka",
        "coco",
        "cocozão",
        "pirok",
        "caraio",
        "vai tomar no cu",
        "vagabunda",
        "vagaba",
        "porra",
        "corno",
        "bixa",
        "bicha",
        "baitola",
        "boquete",
        "bronha",
        "brioco",
        "caga",
        "enrabada",
        "enrabado",
        "cagar",
        "cagado",
        "cagada",
        "arregaçada",
        "arregaçado",
        "bixinha",
        "bichinha",
        "bichona",
        "bixona",
        "arregassado",
        "arregassada",
        "chota",
        "chupada",
        "chupeta",
        "xupeta",
        "grelo",
        "grelinho",
        "greluda",
        "otario",
        "otaria",
        "prega",
        "rabuda",
        "raxada",
        "siririca",
        "tesuda",
        "tezuda",
        "xavasca",
        "chavasca",
        "xibiu",
        "xoxota",
        "chochota"
        ]; 
        
        function temPalavrao(texto) {
            return palavrasProibidas.some(
                palavra => new RegExp(`\\b${palavra}\\b`, 'i').test(texto)
            );
        }

        document.querySelector('form[action="postar_forum_geral.php"]')
            .addEventListener('submit', function(e) {
                const mensagem = this.querySelector('textarea').value;
                
                if (temPalavrao(mensagem)) {
                    e.preventDefault(); 
                    alert("⚠️ Sua mensagem contém linguagem inapropriada. Remova as palavras ofensivas antes de enviar.");
                }
            });
    </script>
</body>
</html>