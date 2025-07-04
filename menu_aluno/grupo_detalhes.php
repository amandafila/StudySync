<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: ../login/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: meus_grupos.php");
    exit;
}

$id_grupo = $_GET['id'];
$id_aluno = $_SESSION['id_aluno'];

$sql_verifica = "SELECT ga.is_adm, g.nome as nome_grupo, g.descricao 
                     FROM grupo_aluno ga
                     JOIN grupo g ON ga.id_grupo = g.id_grupo
                     WHERE ga.id_grupo = ? AND ga.id_aluno = ?";
$stmt = $conexao->prepare($sql_verifica);
$stmt->bind_param("ii", $id_grupo, $id_aluno);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: meus_grupos.php");
    exit;
}

$dados_grupo = $resultado->fetch_assoc();
$is_adm = $dados_grupo['is_adm'];
$nome_grupo = $dados_grupo['nome_grupo'];
$descricao = $dados_grupo['descricao'];

$sql_membros = "SELECT a.id_aluno, a.nome, a.email, ga.is_adm 
                 FROM grupo_aluno ga
                 JOIN aluno a ON ga.id_aluno = a.id_aluno
                 WHERE ga.id_grupo = ?
                 ORDER BY ga.is_adm DESC, a.nome ASC";
$stmt = $conexao->prepare($sql_membros);
$stmt->bind_param("i", $id_grupo);
$stmt->execute();
$membros = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$sql_forum_geral = "SELECT fg.*, a.nome as autor 
                     FROM forum_geral fg
                     JOIN aluno a ON fg.id_aluno = a.id_aluno
                     WHERE fg.id_grupo = ?
                     ORDER BY fg.data_postagem DESC
                     LIMIT 3";
$stmt = $conexao->prepare($sql_forum_geral);
$stmt->bind_param("i", $id_grupo);
$stmt->execute();
$ultimos_posts_geral = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$sql_forum_admins = "SELECT fa.*, a.nome as autor 
                      FROM forum_admins fa
                      JOIN aluno a ON fa.id_aluno = a.id_aluno
                      WHERE fa.id_grupo = ?
                      ORDER BY fa.data_postagem DESC";
$stmt = $conexao->prepare($sql_forum_admins);
$stmt->bind_param("i", $id_grupo);
$stmt->execute();
$ultimos_posts_admins = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($nome_grupo); ?> - StudySync</title>
    <link rel="stylesheet" href="../assets/styles/grupo_detalhes.css">
    <style>
        .forum-container {
            display: none;
        }
        .forum-container.active {
            display: block;
        }
        .btn.active {
            color: #333; 
        }
    </style>
</head>
<body>
    <?php include('../header/header_aluno.php'); ?>
    
    <main>
        <div class="grupo-header">
            <h1><?php echo htmlspecialchars($nome_grupo); ?></h1>
            <p><?php echo htmlspecialchars($descricao); ?></p>
        </div>
        
        <div class="grupo-content">
            <div class="grupo-actions">
                <button id="toggleMembros" class="btn" data-original-text="Mostrar Membros">Mostrar Membros</button>
                <button id="toggleForumGeral" class="btn" data-original-text="Fórum Geral">Fórum Geral</button>
                <button id="toggleForumAdmins" class="btn" data-original-text="Fórum de Admins">Fórum de Admins</button>
            </div>
            
            <div id="membrosContainer" class="forum-container">
                <h2>Membros do Grupo</h2>
                <ul class="membros-list">
                    <?php foreach ($membros as $membro): ?>
                        <li>
                            <div class="membro-info">
                                <span class="membro-nome"><?php echo htmlspecialchars($membro['nome']); ?></span>
                                <span class="membro-email"><?php echo htmlspecialchars($membro['email']); ?></span>
                                <?php if ($membro['is_adm']): ?>
                                    <span class="membro-adm">(Administrador)</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($is_adm && $membro['id_aluno'] != $id_aluno): ?>
                                <form action="remover_membro.php" method="post" class="form-remover">
                                    <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>">
                                    <input type="hidden" name="id_aluno" value="<?php echo $membro['id_aluno']; ?>">
                                    <button type="submit" class="btn-remover">Remover</button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div id="forumGeralContainer" class="forum-container">
                <h2>Fórum Geral</h2>
                
                <form action="postar_forum_geral.php" method="post" class="post-form" enctype="multipart/form-data">
                    <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>">
                    <textarea name="mensagem" placeholder="Escreva sua mensagem..." required></textarea>
                    <div class="file-upload">
                        <label for="arquivo">Anexar PDF:</label>
                        <input type="file" name="arquivo" id="arquivo" accept=".pdf">
                    </div>
                    <button type="submit" class="btn-postar">Postar</button>
                </form>
                
                <div class="posts-list">
                    <?php if (count($ultimos_posts_geral) > 0): ?>
                        <?php foreach ($ultimos_posts_geral as $post): ?>
                            <div class="post">
                                <div class="post-header">
                                    <span class="post-author"><?php echo htmlspecialchars($post['autor']); ?></span>
                                    <span class="post-date"><?php echo date('d/m/Y H:i', strtotime($post['data_postagem'])); ?></span>
                                    <?php if ($is_adm || $post['id_aluno'] == $id_aluno): ?>
                                        <form action="excluir_post.php" method="post" class="form-excluir">
                                            <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                                            <input type="hidden" name="tipo_forum" value="geral">
                                            <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>">
                                            <button type="submit" class="btn-excluir">Excluir</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <div class="post-content">
                                    <?php echo nl2br(htmlspecialchars($post['mensagem'])); ?>
                                    <?php if (!empty($post['arquivo'])): ?>
                                        <div class="post-arquivo">
                                            <a href="../uploads/<?php echo htmlspecialchars($post['arquivo']); ?>" target="_blank">Abrir PDF anexado</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="forum_geral_completo.php?id_grupo=<?php echo $id_grupo; ?>" class="btn ver-mais">Ver todas as postagens</a>
                    <?php else: ?>
                        <p>Nenhuma postagem ainda. Seja o primeiro a contribuir!</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div id="forumAdminsContainer" class="forum-container">
                <h2>Fórum de Administradores</h2>
                
                <?php if ($is_adm): ?>
                    <form action="postar_forum_admins.php" method="post" class="post-form">
                        <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>">
                        <input type="text" name="titulo" placeholder="Título" required>
                        <textarea name="mensagem" placeholder="Escreva sua mensagem..." required></textarea>
                        <button type="submit" class="btn">Postar</button>
                    </form>
                <?php endif; ?>
                
                <div class="posts-list">
                    <?php if (count($ultimos_posts_admins) > 0): ?>
                        <?php foreach ($ultimos_posts_admins as $post): ?>
                            <div class="post">
                                <div class="post-header">
                                    <span class="post-author"><?php echo htmlspecialchars($post['autor']); ?></span>
                                    <span class="post-date"><?php echo date('d/m/Y H:i', strtotime($post['data_postagem'])); ?></span>
                                    <h3 class="post-title"><?php echo htmlspecialchars($post['titulo']); ?></h3>
                                    <?php if ($is_adm || $post['id_aluno'] == $id_aluno): ?>
                                        <form action="excluir_post.php" method="post" class="form-excluir">
                                            <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                                            <input type="hidden" name="tipo_forum" value="admins">
                                            <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>">
                                            <button type="submit" class="btn-excluir-adm">Excluir</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <div class="post-content">
                                    <?php echo nl2br(htmlspecialchars($post['mensagem'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Nenhuma postagem ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
    const palavrasProibidas = [
        "lazarento", "arrombado", "foda", "foda-se", "vai se foder", "vai a merda", "arrombada",
        "fudeo", "fudeu", "gozar", "gozada", "merda", "bosta", "punheta", "filho da puta",
        "filha da puta", "punhetinha", "punheteiro", "fdp", "cuzão", "cusão", "viado", "viadinho",
        "xota", "putaria", "putero", "puto", "puteiro", "bilau", "vadiazinha", "putinha", "babaca",
        "retardado", "cusinho", "cuzinho", "filho de um corno", "rapariga", "rabão", "vadia", "puta",
        "caralho", "broxa", "imbecil", "imbessil", "bastarda", "bastardo", "buceta", "bucetuda",
        "vsf", "vai se foder", "boceta", "cu", "rola", "rolas", "biscate", "pau no cu", "pau no seu cu",
        "pica", "pika", "piroca", "piroka", "coco", "cocozão", "pirok", "caraio", "vai tomar no cu",
        "vagabunda", "vagaba", "porra", "corno", "bixa", "bicha", "baitola", "boquete", "bronha",
        "brioco", "caga", "enrabada", "enrabado", "cagar", "cagado", "cagada", "arregaçada",
        "arregaçado", "bixinha", "bichinha", "bichona", "bixona", "arregassado", "arregassada",
        "chota", "chupada", "chupeta", "xupeta", "grelo", "grelinho", "greluda", "otario", "otaria",
        "prega", "rabuda", "raxada", "siririca", "tesuda", "tezuda", "xavasca", "chavasca", "xibiu",
        "xoxota", "chochota"
    ];

    function temPalavrao(texto) {
        return palavrasProibidas.some(
            palavra => new RegExp(`\\b${palavra}\\b`, 'i').test(texto)
        );
    }

    document.querySelector('form[action="postar_forum_geral.php"]').addEventListener('submit', function(e) {
        const mensagem = this.querySelector('textarea').value;
        const arquivoInput = this.querySelector('input[type="file"]');
        
        if (temPalavrao(mensagem)) {
            e.preventDefault(); 
            alert("⚠️ Sua mensagem contém linguagem inapropriada. Remova as palavras ofensivas antes de enviar.");
            return;
        }
        
        if (arquivoInput.files.length > 0) {
            const arquivo = arquivoInput.files[0];
            const extensao = arquivo.name.split('.').pop().toLowerCase();
            
            if (extensao !== 'pdf' || arquivo.type !== 'application/pdf') {
                e.preventDefault();
                alert("❌ Apenas arquivos PDF são permitidos.");
                return;
            }
            
            if (arquivo.size > 5 * 1024 * 1024) { 
                e.preventDefault();
                alert("❌ O arquivo é muito grande. O tamanho máximo permitido é 5MB.");
                return;
            }
        }
    });

    const containers = {
        'toggleMembros': 'membrosContainer',
        'toggleForumGeral': 'forumGeralContainer',
        'toggleForumAdmins': 'forumAdminsContainer'
    };

    function hideAllContainersAndResetButtons() {
        Object.keys(containers).forEach(buttonId => {
            const container = document.getElementById(containers[buttonId]);
            const button = document.getElementById(buttonId);

            container.classList.remove('active');
            button.classList.remove('active');
            button.textContent = button.dataset.originalText;
        });
    }

    Object.keys(containers).forEach(buttonId => {
        const button = document.getElementById(buttonId);
        const container = document.getElementById(containers[buttonId]);

        button.addEventListener('click', function() {
            if (container.classList.contains('active')) {
                container.classList.remove('active');
                button.classList.remove('active');
                button.textContent = button.dataset.originalText;
            } else {
                hideAllContainersAndResetButtons();

                container.classList.add('active');
                button.classList.add('active');
                button.textContent = 'Ocultar ' + button.dataset.originalText;
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const aba = urlParams.get('aba');
        
        hideAllContainersAndResetButtons();

        if (aba === 'forumGeral') {
            document.getElementById('forumGeralContainer').classList.add('active');
            const button = document.getElementById('toggleForumGeral');
            button.classList.add('active');
            button.textContent = 'Ocultar Fórum Geral';
        } else if (aba === 'forumAdmins') {
            document.getElementById('forumAdminsContainer').classList.add('active');
            const button = document.getElementById('toggleForumAdmins');
            button.classList.add('active');
            button.textContent = 'Ocultar Fórum de Admins';
        } else {
            document.getElementById('membrosContainer').classList.add('active');
            const button = document.getElementById('toggleMembros');
            button.classList.add('active');
            button.textContent = 'Ocultar Membros';
        }
    });
    </script>
</body>
</html>