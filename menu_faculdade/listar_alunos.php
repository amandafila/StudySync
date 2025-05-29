<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Alunos</title>
    <link rel="stylesheet" href="../assets/styles/listar_alunos.css">
</head>
<body>
    <header>
        
        <?php include('../header/header_facul.php'); ?>
    </header>
    <h1>Lista de Alunos</h1>

    <?php
    
    require_once("../conexao/conexao.php");
    require_once('../verifica_sessao/verifica_sessao.php');

    if (!isset($_SESSION['id_faculdade'])) {
        echo "Erro: Não há uma faculdade associada à sua sessão. Verifique o login.";
        exit;
    }

    if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'faculdade') {
        echo "<script>
            alert('Você não está logado!');
            window.location.href = '../login/login.php';
        </script>";
        exit;
    }

    function delete_row($id, $conexao){
        $delete_sql = "DELETE FROM aluno WHERE id_aluno = $id";
        if ($conexao->query($delete_sql)) {
            echo "<script> 
                alert('Deletado com sucesso');
                window.location.href = 'listar_alunos.php';
                </script>";
        } else {
            echo "Erro ao deletar: " . $conexao->error;
        }
    }

    if (isset($_GET['delete_id'])) {
        delete_row($_GET['delete_id'], $conexao);
    }
    
    $id_faculdade = (int) $_SESSION['id_faculdade'];


    $sql = "
        SELECT a.id_aluno, a.email, a.username, a.nome, a.cpf
        FROM aluno a
        JOIN faculdade f ON a.faculdade = f.nome
        WHERE f.id_faculdade = {$id_faculdade}
    ";


    $resultado = $conexao->query($sql);


    echo "<table>";
    echo "<tr>
            <th>Ações</th>
            <th>ID</th>
            <th>Email</th>
            <th>Username</th>
            <th>Nome</th>
            <th>CPF</th>
        </tr>";

    if ($resultado->num_rows > 0) {
        while ($linha = $resultado->fetch_assoc()) {
            echo "<tr>";
            
            echo "<td>
                    <a href='editar_aluno.php?id=" . $linha["id_aluno"] . "'>
                        <button>Alterar</button>
                    </a>
                    <a href='listar_alunos.php?delete_id=" . $linha["id_aluno"] . "' onclick='return confirm(\"Deseja realmente excluir este aluno?\")'>
                        <button>Excluir</button>
                    </a>
                </td>";
                 
            echo "<td>" . $linha["id_aluno"] . "</td>";
            echo "<td>" . $linha["email"] . "</td>";
            echo "<td>" . $linha["username"] . "</td>";
            echo "<td>" . $linha["nome"] . "</td>";
            echo "<td>" . $linha["cpf"] . "</td>";
            echo "</tr>";
            
        }
    } else {
        echo "<tr><td colspan='6'>Nenhum resultado encontrado</td></tr>";
    }

    echo "</table>";
    $conexao->close();
    ?>
</body>
</html>
