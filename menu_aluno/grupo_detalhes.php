<?php
require_once("../conexao/conexao.php");
session_start();

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
                     ORDER BY fa.data_postagem DESC
                     LIMIT 3";
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
                <button id="toggleMembros" class="btn">Mostrar Membros</button>
                <button id="toggleForumGeral" class="btn">Fórum Geral</button>
                <button id="toggleForumAdmins" class="btn">Fórum de Admins</button>
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
                
                <form action="postar_forum_geral.php" method="post" class="post-form">
                    <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>">
                    <textarea name="mensagem" placeholder="Escreva sua mensagem..." required></textarea>
                    <button type="submit" class="btn">Postar</button>
                </form>
                
                <div class="posts-list">
                    <?php if (count($ultimos_posts_geral) > 0): ?>
                        <?php foreach ($ultimos_posts_geral as $post): ?>
                            <div class="post">
                                <div class="post-header">
                                    <span class="post-author"><?php echo htmlspecialchars($post['autor']); ?></span>
                                    <span class="post-date"><?php echo date('d/m/Y H:i', strtotime($post['data_postagem'])); ?></span>
                                </div>
                                <div class="post-content">
                                    <?php echo nl2br(htmlspecialchars($post['mensagem'])); ?>
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
                                </div>
                                <div class="post-content">
                                    <?php echo nl2br(htmlspecialchars($post['mensagem'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="forum_admins_completo.php?id_grupo=<?php echo $id_grupo; ?>" class="btn ver-mais">Ver todas as postagens</a>
                    <?php else: ?>
                        <p>Nenhuma postagem ainda.</p>
                    <?php endif; ?>
                </div>
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

    // Função que detecta palavras ofensivas
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

        const containers = {
            toggleMembros: 'membrosContainer',
            toggleForumGeral: 'forumGeralContainer',
            toggleForumAdmins: 'forumAdminsContainer'
        };
        
        function hideAllContainers() {
            Object.values(containers).forEach(id => {
                document.getElementById(id).style.display = 'none';
            });
        }
        
        function resetButtons() {
            Object.keys(containers).forEach(id => {
                const button = document.getElementById(id);
                button.textContent = button.textContent.replace('Ocultar ', '');
            });
        }
        
        Object.entries(containers).forEach(([buttonId, containerId]) => {
            const button = document.getElementById(buttonId);
            const container = document.getElementById(containerId);
            
            button.addEventListener('click', function() {
                if (container.style.display === 'block') {
                    container.style.display = 'none';
                    this.textContent = this.textContent.replace('Ocultar ', '');
                } else {
                    hideAllContainers();
                    resetButtons();
                    container.style.display = 'block';
                    this.textContent = 'Ocultar ' + this.textContent;
                }
            });
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const aba = urlParams.get('aba');
            
            if (aba === 'forumGeral') {
                hideAllContainers();
                document.getElementById('forumGeralContainer').style.display = 'block';
                document.getElementById('toggleForumGeral').textContent = 'Ocultar Fórum Geral';
            } else if (aba === 'forumAdmins') {
                hideAllContainers();
                document.getElementById('forumAdminsContainer').style.display = 'block';
                document.getElementById('toggleForumAdmins').textContent = 'Ocultar Fórum de Admins';
            }
        });
    </script>
</body>
</html>