<?php
require_once("../conexao/conexao.php");
session_start();

if (!isset($_SESSION['id_aluno'])) {
    echo "Erro: Faça login.";
    exit;
}

$id_admin = $_SESSION['id_aluno'];

$sql = "SELECT 
            sg.id_solicitacao,
            sg.mensagem,
            sg.data_solicitacao,
            sg.id_aluno,
            a.nome AS nome_aluno,
            g.nome AS nome_grupo
        FROM solicitacao_grupo sg
        JOIN grupo g ON sg.id_grupo = g.id_grupo
        JOIN aluno a ON sg.id_aluno = a.id_aluno
        JOIN grupo_aluno ga ON ga.id_grupo = g.id_grupo
        WHERE ga.id_aluno = $id_admin
          AND ga.is_adm = 1
          AND sg.status = 'pendente'";

$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Solicitações de Entrada</title>
</head>
<body>
    <h2>Solicitações pendentes nos seus grupos</h2>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>";
            echo "<strong>Grupo:</strong> " . $row['nome_grupo'] . "<br>";
            echo "<strong>Aluno:</strong> " . $row['nome_aluno'] . "<br>";
            echo "<strong>Mensagem:</strong> " . $row['mensagem'] . "<br>";
            echo "<form action='responder_solicitacao.php' method='POST'>
                    <input type='hidden' name='id_solicitacao' value='" . $row['id_solicitacao'] . "'>
                    <button name='acao' value='aprovar'>Aprovar</button>
                    <button name='acao' value='rejeitar'>Rejeitar</button>
                  </form>";
            echo "</div>";
        }
    } else {
        echo "<p>Não há solicitações pendentes.</p>";
    }
    ?>
</body>
</html>
