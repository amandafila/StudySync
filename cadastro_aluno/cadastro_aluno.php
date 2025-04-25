<?php
require_once("../conexao/conexao.php");

$erros = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $senha_raw = $_POST['senha'];


    if (empty($nome)) $erros[] = "O  nome é obrigatório.";
    if (empty($cpf)) $erros[] = "O  CPF é obrigatório.";
    if (empty($usuario)) $erros[] = "O  usuário é obrigatório.";
    if (empty($email)) $erros[] = "o  email é obrigatório.";
    if (empty($senha_raw)) $erros[] = "a  senha é obrigatório.";


    if (empty($erros)) {
        $check = "SELECT * FROM aluno WHERE username = '$usuario' OR email = '$email'";
        $result = $conexao->query($check);
        if ($result && $result->num_rows > 0) {
            $erros[] = "Usuário ou email já cadastrado.";
        }
    }

    if (empty($erros)) {
        $senha = password_hash($senha_raw, PASSWORD_DEFAULT);
        $sql_aluno = "INSERT INTO aluno (nome, username, email, senha, cpf) 
                      VALUES ('$nome', '$usuario', '$email', '$senha', '$cpf')";
        if ($conexao->query($sql_aluno)) {
            header("Location: sucesso_aluno.php");
            exit();
        } else {
            $erros[] = "Erro ao cadastrar aluno: " . $conexao->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Cadastro de Estudante</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../assets/styles/cadastro_aluno.css" rel="stylesheet">
</head>

<body>
  <div class="left">
    <div class="div_left_title">
      <h5 class="title_left">StudySync</h5>
    </div>
    <div class="div_left_paragraph">
      <p class="left_paragraph">De estudantes,  <br> para estudantes.</p>
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

    <h2 class="subtitulo_cadastro">Faça já seu cadastro</h2>

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
          <div class="div_paragrafo">
            <p class="paragros_left">Nome</p>
          </div>
          <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">Usuário</p>
          </div>
          <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">CPF</p>
          </div>
          <input type="text" name="cpf" class="form-control" required>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">Email</p>
          </div>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">Senha</p>
          </div>
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
