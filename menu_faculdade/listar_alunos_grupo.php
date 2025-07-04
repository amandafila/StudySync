<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Alunos do Grupo</title>
    <link rel="stylesheet" href="../assets/styles/listar_alunos_grupo.css" />
</head>
<body>
    <?php include('../header/header_facul.php'); ?>

    <h1>Alunos do Grupo</h1>

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

    if (isset($_GET['id_grupo'])) {
        $idGrupo = intval($_GET['id_grupo']);

        $sql = "SELECT a.id_aluno, a.email, a.username, a.nome, a.cpf
                FROM aluno a
                INNER JOIN grupo_aluno ga ON a.id_aluno = ga.id_aluno
                WHERE ga.id_grupo = $idGrupo";

        $resultado = $conexao->query($sql);

        echo "<table>";
        echo "<tr>
                <th>ID</th>
                <th>Email</th>
                <th>Username</th>
                <th>Nome</th>
                <th>CPF</th>
                <th></th>
            </tr>";

        if ($resultado->num_rows > 0) {
            while ($linha = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $linha["id_aluno"] . "</td>";
                echo "<td>" . $linha["email"] . "</td>";
                echo "<td>" . $linha["username"] . "</td>";
                echo "<td>" . $linha["nome"] . "</td>";
                echo "<td>" . $linha["cpf"] . "</td>";
                echo "<td style='display: flex; gap: 10px; justify-content: center;'>
                        <a href='listar_alunos_grupo.php?id_grupo=$idGrupo&delete_id=" . $linha["id_aluno"] . "' style='text-decoration: none;'>
                            <button>Remover</button>
                        </a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Nenhum aluno encontrado para este grupo.</td></tr>";
        }

        echo "</table>";
        $conexao->close();
    } else {
        echo "<p>ID do grupo não foi informado.</p>";
    }
    ?>
</body>
</html>
