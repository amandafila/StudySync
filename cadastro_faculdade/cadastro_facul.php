<?php
require_once("../conexao/conexao.php");

$erros = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $cnpj = trim($_POST['cnpj']);
    $cep = trim($_POST['cep']);
    $telefone = trim($_POST['telefone']);
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $senha_raw = $_POST['senha'];


    if (empty($nome)) $erros[] = "O campo Nome é obrigatório.";
    if (empty($cnpj)) $erros[] = "O campo CNPJ é obrigatório.";
    if (empty($cep)) $erros[] = "O campo CEP é obrigatório.";
    if (empty($telefone)) $erros[] = "O campo Telefone é obrigatório.";
    if (empty($usuario)) $erros[] = "O campo Usuário é obrigatório.";
    if (empty($email)) $erros[] = "O campo Email é obrigatório.";
    if (empty($senha_raw)) $erros[] = "O campo Senha é obrigatório.";

    if (empty($erros)) {
        $check = "SELECT * FROM faculdade WHERE username = '$usuario' OR email = '$email'";
        $result = $conexao->query($check);
        if ($result && $result->num_rows > 0) {
            $erros[] = "Usuário ou email já cadastrado.";
        }
    }

    if (empty($erros)) {
        $senha = password_hash($senha_raw, PASSWORD_DEFAULT);
        $sql_faculdade = "INSERT INTO faculdade (nome, username, email, senha, cnpj, cep, telefone) 
                          VALUES ('$nome', '$usuario', '$email', '$senha', '$cnpj', '$cep', '$telefone')";
        if ($conexao->query($sql_faculdade)) {
            header("Location: sucesso_facul.php");
            exit();
        } else {
            $erros[] = "Erro ao cadastrar faculdade: " . $conexao->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Cadastro de Faculdade</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../assets/styles/cadastro_facul.css" rel="stylesheet">
</head>

<body>
  <div class="left">
    <div class="div_left_title">
      <h5 class="title_left">StudySync</h5>
    </div>
    <div class="div_left_paragraph">
      <p class="left_paragraph">Faça parte dessa <br> comunidade <br> incrível.</p>
    </div>
  </div>

  <div class="right">
    <div class="div_header">
      <div class="header_sobre">
        <a href="#" class="link_right">Sobre</a>
      </div>
      <div class="header_login">
        <a class="link_right" href="../login/login.php">Login</a>
      </div>
    </div>

    <h2 class="subtitulo_cadastro">Cadastre a sua instituição</h2>

    <?php if (!empty($erros)): ?>
      <div style="color: red; margin-bottom: 10px;">
        <ul>
          <?php foreach ($erros as $erro): ?>
            <li><?= htmlspecialchars($erro) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="col-6">
      <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-2">
          <p class="paragros_left">Nome</p>
          <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">CNPJ</p>
          <input type="text" name="cnpj" class="form-control" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">CEP</p>
          <input type="text" name="cep" class="form-control" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">Telefone</p>
          <input type="text" name="telefone" class="form-control" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">Usuário</p>
          <input type="text" name="usuario" class="form-control" >
        </div>
        <div class="mb-2">
          <p class="paragros_left">Email</p>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">Senha</p>
          <input type="password" name="senha" class="form-control" required>
        </div>
        <div class="mb-2">
          <button type="submit" class="mt-2 btn btn-primary">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
