<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos do Grupo</title>
    <link rel="stylesheet" href="../assets/styles/listar_alunos_grupo.css">
</head>
<body>
    <header>
        <h1>Alunos do Grupo</h1>
    </header>

<?php
require_once("../conexao/conexao.php");

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
            <th></th> <!-- só pra alinhar os botões -->
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
                    <a href='editar_aluno.php?id=" . $linha["id_aluno"] . "' style='text-decoration: none;'>
                        <button>Alterar</button>
                    </a>
                    <a href='listar_alunos_grupo.php?id_grupo=$idGrupo&delete_id=" . $linha["id_aluno"] . "' style='text-decoration: none;'>
                        <button>remover</button>
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
