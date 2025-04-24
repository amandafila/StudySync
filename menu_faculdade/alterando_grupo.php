<?php
require_once("../conexao/conexao.php");
session_start();

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
    $novaDescricao = $_POST['descricao'];
    $novoEmailAdm = $_POST['adm'];

    if (empty($novaDescricao) || empty($novoEmailAdm)) {
        $erros[] = "Todos os campos são obrigatórios.";
    } else {
        $sqlBuscaAdm = "SELECT id_aluno FROM aluno WHERE email = '$novoEmailAdm'";
        $resultadoAdm = $conexao->query($sqlBuscaAdm);

        if ($resultadoAdm->num_rows === 0) {
            $erros[] = "Administrador com esse e-mail não encontrado.";
        } else {
            $idNovoAdm = $resultadoAdm->fetch_assoc()['id_aluno'];

            $conexao->query("UPDATE grupo SET descricao = '$novaDescricao' WHERE id_grupo = $idGrupo");
            $conexao->query("UPDATE grupo_aluno SET is_adm = 0 WHERE id_grupo = $idGrupo");

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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Grupo</title>
    <link rel="stylesheet" href="../assets/styles/alterando_grupo.css">
</head>
<body>
    <div class="grupo-container">
        <h1>Alterar Grupo</h1>

        <?php if (!empty($erros)): ?>
            <div style="color: red; margin-bottom: 15px;">
                <?php foreach ($erros as $erro) echo "<p>$erro</p>"; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <p><strong>Nome do Grupo:</strong> <?= htmlspecialchars($grupo['nome']) ?></p>
            <textarea name="descricao" placeholder="Nova descrição"
                oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';" required><?= htmlspecialchars($grupo['descricao']) ?></textarea><br><br>
            <input type="email" name="adm" placeholder="Novo email do administrador"
                   value="<?= htmlspecialchars($grupo['email_adm']) ?>" required><br><br>
            <button type="submit">Salvar alterações</button>
        </form>
    </div>
</body>
</html>
