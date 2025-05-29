<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    echo "<script>
        alert('Você não está logado como aluno!');
        window.location.href = '../login/login.php';
    </script>";
    exit;
}

$idAluno = $_SESSION['id_aluno'];
$erros = [];

$sqlAluno = "SELECT nome, username, email FROM aluno WHERE id_aluno = $idAluno";
$result = $conexao->query($sqlAluno);
$aluno = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novoNome = $conexao->real_escape_string($_POST['nome']);
    $novoUsername = $conexao->real_escape_string($_POST['username']);

    if (empty($novoNome) || empty($novoUsername)) {
        $erros[] = "Todos os campos são obrigatórios.";
    } else {
        $sqlVerificaUsername = "SELECT id_aluno FROM aluno WHERE username = '$novoUsername' AND id_aluno != $idAluno";
        $resultadoUsername = $conexao->query($sqlVerificaUsername);

        if ($resultadoUsername->num_rows > 0) {
            $erros[] = "Este nome de usuário já está em uso por outro aluno.";
        } else {
            $updateAluno = $conexao->query("UPDATE aluno SET nome = '$novoNome', username = '$novoUsername' WHERE id_aluno = $idAluno");
            
            if (!$updateAluno) {
                $erros[] = "Erro ao atualizar os dados: " . $conexao->error;
            } else {
                echo "<script>
                    alert('Perfil atualizado com sucesso!');
                    window.location.href = 'editar_perfil.php';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySync - Editar Perfil</title>
    <link rel="stylesheet" href="../assets/styles/editar_perfil.css">
</head>
<body>
    <?php include('../header/header_aluno.php'); ?>
    
    <div class="perfil-container">
        <h1>Editar Perfil</h1>

        <?php if (!empty($erros)): ?>
            <div class="erros">
                <?php foreach ($erros as $erro) echo "<p>$erro</p>"; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($aluno['nome']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="username">Nome de Usuário</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($aluno['username']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($aluno['email']) ?>" disabled>
                <small>Para alterar o e-mail, entre em contato com o suporte</small>
            </div>
            
            <div class="form-actions">
                <a href="solicitar_alteracao_senha.php" class="botao-alterar-senha">Alterar Senha</a>
                <button type="submit" class="botao-salvar">Salvar Alterações</button>
            </div>
        </form>
    </div>
</body>
</html>