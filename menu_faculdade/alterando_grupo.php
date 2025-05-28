<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'faculdade') {
    echo "<script>
        alert('Você não está logado!');
        window.location.href = '../login/login.php';
    </script>";
    exit;
}

if (!isset($_GET['id_grupo'])) {
    echo "ID do grupo não fornecido.";
    exit;
}

$idGrupo = intval($_GET['id_grupo']);
$erros = [];

$sqlGrupo = "SELECT g.nome, g.descricao, a.email AS email_adm
            FROM grupo g
            LEFT JOIN grupo_aluno ga ON g.id_grupo = ga.id_grupo AND ga.is_adm = 1
            LEFT JOIN aluno a ON ga.id_aluno = a.id_aluno
            WHERE g.id_grupo = $idGrupo";

$result = $conexao->query($sqlGrupo);
$grupo = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novaDescricao = $conexao->real_escape_string($_POST['descricao']);
    $novoEmailAdm = $conexao->real_escape_string($_POST['adm']);
    $novoNome = $conexao->real_escape_string($_POST['nome_grupo']);

    if (empty($novaDescricao) || empty($novoEmailAdm) || empty($novoNome)) {
        $erros[] = "Todos os campos são obrigatórios.";
    } else {
        $sqlBuscaAdm = "SELECT id_aluno FROM aluno WHERE email = '$novoEmailAdm'";
        $resultadoAdm = $conexao->query($sqlBuscaAdm);

        $sqlBuscaNomeGrupo = "SELECT id_grupo FROM grupo WHERE nome = '$novoNome' AND id_grupo != $idGrupo";
        $resultadoNome = $conexao->query($sqlBuscaNomeGrupo);

        if ($resultadoAdm->num_rows === 0) {
            $erros[] = "Administrador com esse e-mail não encontrado.";
        } elseif($resultadoNome->num_rows > 0) {
            $erros[] = "Você já possui um grupo com este nome";
        } else {
            $idNovoAdm = $resultadoAdm->fetch_assoc()['id_aluno'];

            // Atualiza nome e descrição em uma única query
            $updateGrupo = $conexao->query("UPDATE grupo SET nome = '$novoNome', descricao = '$novaDescricao' WHERE id_grupo = $idGrupo");
            
            if (!$updateGrupo) {
                $erros[] = "Erro ao atualizar os dados do grupo: " . $conexao->error;
            } else {
                // Remove o ADM atual
                $conexao->query("UPDATE grupo_aluno SET is_adm = 0 WHERE id_grupo = $idGrupo");
                
                // Verifica se o novo ADM já está no grupo
                $sqlAdmExiste = "SELECT * FROM grupo_aluno WHERE id_grupo = $idGrupo AND id_aluno = $idNovoAdm";
                $resultadoExiste = $conexao->query($sqlAdmExiste);

                if ($resultadoExiste->num_rows > 0) {
                    $conexao->query("UPDATE grupo_aluno SET is_adm = 1 WHERE id_grupo = $idGrupo AND id_aluno = $idNovoAdm");
                } else {
                    $conexao->query("INSERT INTO grupo_aluno (id_grupo, id_aluno, is_adm) VALUES ($idGrupo, $idNovoAdm, 1)");
                }

                echo "<script>
                    alert('Grupo alterado com sucesso!');
                    window.location.href = 'grupo.php?id_grupo=$idGrupo';
                </script>";
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Grupo</title>
    <link rel="stylesheet" href="../assets/styles/alterando_grupo.css">
</head>
<body>
    <?php include('../header/header_facul.php'); ?>
    <div class="grupo-container">
        <h1>Alterar Grupo</h1>

        <?php if (!empty($erros)): ?>
            <div style="color: red; margin-bottom: 15px;">
                <?php foreach ($erros as $erro) echo "<p>$erro</p>"; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <p><strong>Nome do Grupo:</strong></p> 
            <textarea name="nome_grupo" oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';" required><?= htmlspecialchars($grupo['nome']) ?></textarea><br><br>
            
            <p><strong>Descrição: </strong></p>
            <textarea name="descricao" placeholder="Nova descrição" oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';" required><?= htmlspecialchars($grupo['descricao']) ?></textarea><br><br>
            
            <p><strong>Email ADM: </strong></p>
            <input type="email" name="adm" placeholder="Novo email do administrador" value="<?= htmlspecialchars($grupo['email_adm']) ?>" required><br><br>
            
            <button type="submit">Salvar alterações</button>
        </form>
    </div>
</body>
</html>