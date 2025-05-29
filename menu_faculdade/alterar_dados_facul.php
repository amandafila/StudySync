<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

$erros = [];

if (!isset($_SESSION['usuario'])) {
    echo "<script>
        alert('Você não está logado!');
        window.location.href = '../login/login.php';
        </script>";
    exit; 
}

if ($_SESSION['tipo'] !== 'faculdade') {
    echo "<script>
        alert('Acesso restrito a faculdades!');
        window.location.href = '../login/login.php';
        </script>";
    exit;
}

$id_faculdade = $_SESSION['id_faculdade'];

$sqlFaculdade = "SELECT nome, username, email, cnpj, cep, telefone FROM faculdade WHERE id_faculdade = $id_faculdade";
$result = $conexao->query($sqlFaculdade);
$faculdade = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novoNome = $conexao->real_escape_string($_POST['nome']);
    $novoUsername = $conexao->real_escape_string($_POST['username']);
    $novoCep = $conexao->real_escape_string($_POST['cep']);
    $novoTelefone = $conexao->real_escape_string($_POST['telefone']);

    if (empty($novoNome) || empty($novoUsername) || empty($novoCep) || empty($novoTelefone)) {
        $erros[] = "Todos os campos são obrigatórios.";
    } else {
        $sqlVerificaUsername = "SELECT id_faculdade FROM faculdade WHERE username = '$novoUsername' AND id_faculdade != $id_faculdade";
        $resultadoUsername = $conexao->query($sqlVerificaUsername);

        if ($resultadoUsername->num_rows > 0) {
            $erros[] = "Este nome de usuário já está em uso por outra faculdade.";
        } else {
            $updateFaculdade = $conexao->query("UPDATE faculdade SET 
                nome = '$novoNome', 
                username = '$novoUsername', 
                cep = '$novoCep', 
                telefone = '$novoTelefone' 
                WHERE id_faculdade = $id_faculdade");
            
            if (!$updateFaculdade) {
                $erros[] = "Erro ao atualizar os dados: " . $conexao->error;
            } else {
                echo "<script>
                    alert('Perfil atualizado com sucesso!');
                    window.location.href = 'alterar_dados_facul.php';
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
    <?php include('../header/header_facul.php'); ?>
    
    <div class="perfil-container">
        <h1>Editar Perfil da Faculdade</h1>

        <?php if (!empty($erros)): ?>
            <div class="erros">
                <?php foreach ($erros as $erro) echo "<p>$erro</p>"; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome da Faculdade</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($faculdade['nome']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="username">Nome de Usuário</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($faculdade['username']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($faculdade['email']) ?>" disabled>
                <small>Para alterar o e-mail, entre em contato com o suporte</small>
            </div>
            
            <div class="form-group">
                <label for="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" value="<?= htmlspecialchars($faculdade['cnpj']) ?>" disabled>
            </div>
            
            <div class="form-group">
                <label for="cep">CEP</label>
                <input type="text" id="cep" name="cep" value="<?= htmlspecialchars($faculdade['cep']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($faculdade['telefone']) ?>" required>
            </div>
            
            <div class="form-actions">
                <a href="solicitar_alteracao_senha.php" class="botao-alterar-senha">Alterar Senha</a>
                <button type="submit" class="botao-salvar">Salvar Alterações</button>
            </div>
        </form>
    </div>
</body>
</html>