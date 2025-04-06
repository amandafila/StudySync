<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>CRUD Faculdades</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container-fluid mt-3">
    <div class="row mb-4">
      <div class="col-md-6">
        <h4><i class="fa fa-university"></i> CRUD Faculdades</h4>
      </div>
      <div class="col-md-6 text-end">
        <a href="cadastro_facul.php" class="btn btn-success">
          <i class="fa fa-plus"></i> Nova Faculdade
        </a>
      </div>
    </div>
    
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>Ações</th>
            <th>ID</th>
            <th>Usuário</th>
            <th>E-mail</th>
            <th>CNPJ</th>
          </tr>
        </thead>
        <tbody>
<?php
require_once("conexao.php");

if ($conexao->connect_error) {
  die("<tr><td colspan='5' class='text-danger'>Erro de conexão: " . $conexao->connect_error . "</td></tr>");
}

$query = "SELECT u.id, u.username, u.email, f.cnpj 
          FROM usuario u
          JOIN faculdade f ON u.id = f.id_usuario";

$resultado = $conexao->query($query);

if ($resultado->num_rows > 0) {
  while($faculdade = $resultado->fetch_assoc()) {
    echo "<tr>";
    echo "<td>
            <a href='update_facul.php?id=".$faculdade['id']."' class='btn btn-sm btn-primary'>
              <i class='fa fa-edit'></i> Editar
            </a>
            <a href='delete.php?id=".$faculdade['id']."' class='btn btn-sm btn-danger' onclick='return confirm(\"Tem certeza?\")'>
                <i class='fa fa-trash'></i> Excluir
            </a>

          </td>";
    echo "<td>".$faculdade['id']."</td>";
    echo "<td>".htmlspecialchars($faculdade['username'])."</td>";
    echo "<td>".htmlspecialchars($faculdade['email'])."</td>";
    echo "<td>".htmlspecialchars($faculdade['cnpj'])."</td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='5' class='text-center'>Nenhuma faculdade cadastrada</td></tr>";
}

$conexao->close();
?>
