<!DOCTYPE html>
<html lang="en">
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
      <p class="left_paragraph">Faça parte dessa <br> comunidade <br> incrível. </p>
    </div>
  </div>
  <div class="right">
  <div  class="div_header">
      <div class="header_sobre">
        <a href="#" class="link_right">Sobre</a>
      </div>
      <div class="header_login">
        <a class="link_right" href="#">Login</a>
      </div>
    </div>
    <h2 class="subtitulo_cadastro">Cadastre a sua instituição</h2>
    <div class="col-6">
      <form method="POST" action="cadastro_facul.php" enctype="multipart/form-data">
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">Nome</p>
          </div>
          <input type="text" name="nome" class="form-control"  required>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">CNPJ</p>
          </div>
          <input type="text" name="cnpj" class="form-control" required>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">CEP</p>
          </div>
          <input type="text" name="cep" class="form-control"  required>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">Telefone</p>
          </div>
          <input type="text" name="telefone" class="form-control"  required>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">Usuário</p>
          </div>
          <input type="text" name="usuario" class="form-control"  required>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">Email</p>
          </div>
          <input type="email" name="email" class="form-control"  required>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo">
            <p class="paragros_left">Senha</p>
          </div>
          <input type="password" name="senha" class="form-control"  required>
        </div>
        <div class="mb-2 documentacao_div">
          <input type="file" name="documentacao" class="envio_documentos" placeholder="Documentação">
        </div>
        <div class="mb-2">
          <button type="submit" class="mt-2 btn btn-primary">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
<?php
$conexao = new mysqli("127.0.0.1", "root", "", "studysync");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cnpj = $_POST['cnpj'];
    $cep = $_POST['cep'];
    $telefone = $_POST['telefone'];
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    
    $sql_usuario = "INSERT INTO usuario (nome, username, email, senha) 
    VALUES ('$nome', '$usuario', '$email', '$senha')";
    
    if ($conexao->query($sql_usuario)) {
        $id_usuario = $conexao->insert_id;
        $sql_faculdade = "INSERT INTO faculdade (cnpj, cep, telefone, id_usuario) 
        VALUES ('$cnpj', '$cep', '$telefone', '$id_usuario')";
        
        if ($conexao->query($sql_faculdade)) {
            header("Location: ../menu/index.html");
            exit();
        } else {
            echo "Erro ao cadastrar faculdade: " . $conexao->error;
        }
    } else {
        echo "Erro ao cadastrar usuário: " . $conexao->error;
    }
}
?>
