
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/listar_alunos.css?">

    <title>Lista de alunos</title>
</head>
<body>
    <?php include('../header/header_aluno.php'); ?>
<?php
    require_once("../conexao/conexao.php");
    require_once('../verifica_sessao/verifica_sessao.php');
    
    function delete_row($id, $conexao){
        $delete_sql ="DELETE FROM aluno WHERE id_aluno = $id";
            if ($conexao->query($delete_sql)) {
                echo"<script> 
                alert('Deletado com sucesso');
                window.location.href = 'listar_alunos.php';
                </script>";
            } else {
                echo "Erro ao deletar: " . $conexao->error;
            }
        }

    if(isset($_GET['delete_id'])){
        delete_row($_GET['delete_id'],$conexao);
    }
    
    $sql = "SELECT id_aluno, email, username, nome, cpf FROM aluno";
    $resultado = $conexao->query($sql);
    echo"<table border='1' style='width:600px;'>";
    echo"<tr>
            <th>#</th>
            <th>ID</th>
            <th>Email</th>
            <th>Username</th>
            <th>Nome</th>
            <th>CPF</th>
        </tr>";
    if($resultado->num_rows > 0){
        while($linha = $resultado->fetch_assoc()){
            echo"<tr>";
            echo "<td style='display: flex; gap: 10px;'>
                    <a href='editar_aluno.php?id=".$linha["id_aluno"]."' style='text-decoration: none;'>
                        <button style='padding: 5px 10px;'>Alterar</button>
                    </a>
                    <a href='listar_alunos.php?delete_id=".$linha["id_aluno"]."' style='text-decoration: none;'>
                        <button style='padding: 5px 10px;'>Excluir</button>
                    </a>
                </td>";
            
            echo "<td>" . $linha["id_aluno"] . "</td>";
            echo "<td>" . $linha["email"] . "</td>";
            echo "<td>" . $linha["username"] . "</td>";
            echo "<td>" . $linha["nome"] . "</td>";
            echo "<td>" . $linha["cpf"] . "</td>";
            echo "</tr>";
        }
    }else{
        echo "<tr><td colspan='3'>Nenhum resultado</td></tr>";
    }
    echo "</table>";
    $conexao->close();
?>
</body>
</html>