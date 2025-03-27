

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Cadastro de Faculdade</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <div class="mt-2 container-fluid">
    <div class="row">
      <div class="col-1">
        <i class="fa fa-check" style="font-size:30px;"></i>
      </div>
      <div class="col-2">
        <h5>Cadastro de Faculdade</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-6">
        <form method="POST" action="cadastro_facul.php" enctype="multipart/form-data">
          <div class="mb-2">
            <input type="text" name="nome" class="form-control" placeholder="Nome da faculdade" required>
          </div>
          <div class="mb-2">
            <input type="text" name="cnpj" class="form-control" placeholder="CNPJ" required>
          </div>
          <div class="mb-2">
            <input type="text" name="cep" class="form-control" placeholder="CEP" required>
          </div>
          <div class="mb-2">
            <input type="text" name="telefone" class="form-control" placeholder="Telefone" required>
          </div>
          <div class="mb-2">
            <input type="text" name="usuario" class="form-control" placeholder="Usuário" required>
          </div>
          <div class="mb-2">
            <input type="email" name="email" class="form-control" placeholder="E-mail" required>
          </div>
          <div class="mb-2">
            <input type="password" name="senha" class="form-control" placeholder="Senha" required>
          </div>
          <div class="mb-2">
            <input type="file" name="documentacao" class="form-control" placeholder="Documentação">
          </div>
          <button type="submit" class="mt-2 btn btn-primary">Cadastrar</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
<?php
function conecta_db() {
  $server = "127.0.0.1";
  $user = "root"; 
  $pass = ""; 
  $db_name = "studysync"; 

  $conexao = new mysqli($server, $user, $pass, $db_name);
    return $conexao;
}

if (isset($_POST['nome'])) {
    $obj = conecta_db();
    $nome = $_POST['nome'];
    $cnpj = $_POST['cnpj'];
    $cep = $_POST['cep'];
    $telefone = $_POST['telefone'];
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $documentacao = $_FILES['documentacao']['tmp_name'] ? file_get_contents($_FILES['documentacao']['tmp_name']) : null;


    $query = "
    INSERT INTO cadastro_faculdade (nome, cnpj, cep, telefone, usuario, email, senha, documentacao) 
    VALUES ('$nome', '$cnpj', '$cep', '$telefone', '$usuario', '$email', '$senha', ?)";
    

    $stmt = $obj->prepare($query);
    $stmt->bind_param("s", $documentacao);  
    

    $resultado = $stmt->execute();
    
    if ($resultado) {
        header("location:../menu/index.html");  
    } else {
        echo "<span class='alert alert-danger'>
        <h5>Não funcionou!</h5>
        </span>";
    }
}
?>
