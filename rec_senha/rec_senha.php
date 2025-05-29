<?php
require_once("../conexao/conexao.php");

session_start();

$erros = [];
$etapa = isset($_GET['etapa']) ? $_GET['etapa'] : 'inicio';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($etapa == 'inicio') {
        // Etapa 1: Verificar usuário e chave de recuperação
        $usuario = trim($_POST['usuario']);
        $chave_recuperacao = trim($_POST['chave_recuperacao']);

        if (empty($usuario) || empty($chave_recuperacao)) {
            $erros[] = "Todos os campos são obrigatórios.";
        } else {
            $sql = "SELECT id_aluno, chave_recuperacao_hash FROM aluno WHERE username = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $erros[] = "Usuário não encontrado.";
            } else {
                $aluno = $result->fetch_assoc();
                if (!password_verify($chave_recuperacao, $aluno['chave_recuperacao_hash'])) {
                    $erros[] = "Chave de recuperação inválida.";
                } else {
                    $_SESSION['id_aluno_recuperacao'] = $aluno['id_aluno'];
                    $etapa = 'nova_senha';
                }
            }
            $stmt->close();
        }
    } elseif ($etapa == 'nova_senha') {
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];

        if (empty($nova_senha) || empty($confirmar_senha)) {
            $erros[] = "Todos os campos são obrigatórios.";
        } elseif (strlen($nova_senha) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $nova_senha)) {
            $erros[] = "A senha deve ter no mínimo 8 caracteres e conter pelo menos um caractere especial.";
        } elseif ($nova_senha !== $confirmar_senha) {
            $erros[] = "As senhas não coincidem.";
        } else {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $sql = "UPDATE aluno SET senha = ? WHERE id_aluno = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("si", $senha_hash, $_SESSION['id_aluno_recuperacao']);
            
            if ($stmt->execute()) {
                session_destroy();
                header("Location: ../login/login.php?senha_alterada=1");
                exit();
            } else {
                $erros[] = "Erro ao atualizar a senha. Tente novamente.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Recuperação de Senha - StudySync</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/styles/rec.css">
</head>
<body>
    <div class="container-form">
        <h1>Recuperação de Senha</h1>
        
        <?php if (!empty($erros)): ?>
            <div class="erros">
                <?php foreach ($erros as $erro): ?>
                    <p><?= htmlspecialchars($erro) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <?php if ($etapa == 'inicio'): ?>
                <form method="POST" action="rec_senha.php?etapa=inicio">
                    <label for="usuario">Nome de Usuário</label>
                    <input type="text" id="usuario" name="usuario" required>
                    
                    <label for="chave_recuperacao">Chave de Recuperação</label>
                    <input placeholder="Chave recebida durante o cadastro" type="text" id="chave_recuperacao" name="chave_recuperacao" required>
                    
                    <button type="submit">Verificar</button>
                </form>
            <?php elseif ($etapa == 'nova_senha'): ?>
                <form method="POST" action="rec_senha.php?etapa=nova_senha">
                    <label for="nova_senha">Nova Senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" required>
                    
                    <label for="confirmar_senha">Confirmar Nova Senha</label>
                    <input placeholder="8 dígitos e 1 caractere especial" type="password" id="confirmar_senha" name="confirmar_senha" required>
                    
                    <button type="submit">Redefinir Senha</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="voltar-login">
            <a href="../login/login.php">Voltar para o Login</a>
        </div>
    </div>
</body>
</html>