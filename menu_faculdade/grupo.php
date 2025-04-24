<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Grupo</title>
    <link rel="stylesheet" href="../assets/styles/grupo.css">
</head>
<body>
<?php
    require_once("../conexao/conexao.php");
    if (isset($_GET['id_grupo'])) {
        $idGrupo = intval($_GET['id_grupo']);

        if ($conexao->connect_error) {
            die("Erro de conexão: " . $conexao->connect_error);
        }

        $sql = "SELECT g.nome AS nome_grupo, g.descricao, a.username, a.email 
                FROM grupo g
                LEFT JOIN grupo_aluno ga ON g.id_grupo = ga.id_grupo AND ga.is_adm = 1
                LEFT JOIN aluno a ON ga.id_aluno = a.id_aluno
                WHERE g.id_grupo = $idGrupo";

        $resultado = $conexao->query($sql);

        if ($resultado->num_rows > 0) {
            $grupo = $resultado->fetch_assoc();
?>
    <header class="cabecalho">
        <h1><?php echo htmlspecialchars($grupo['nome_grupo']); ?></h1>
    </header>

    <div class="div_corpo">
        <div class="div_corpo_div">
            <p><strong>Descrição:</strong> <?php echo htmlspecialchars($grupo['descricao']); ?></p>

            <div class="admin-info">
                <h3>Administrador:</h3>
                <p><strong>Usuário:</strong> <?php echo htmlspecialchars($grupo['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($grupo['email']); ?></p>
            </div>

            <div class="acoes-grupo">
                <button onclick="window.location.href='alterando_grupo.php?id_grupo=<?php echo $idGrupo; ?>'">Alterar Grupo</button>
                <button onclick="window.location.href='listar_alunos_grupo.php?id_grupo=<?php echo $idGrupo; ?>'">Listar Alunos</button>

                <form action="excluir_grupo.php" method="POST" onsubmit="return confirmarExclusao();" style="display:inline;">
                    <input type="hidden" name="id_grupo" value="<?php echo $idGrupo; ?>">
                    <button type="submit">Excluir Grupo</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function confirmarExclusao() {
            return confirm("Certeza que quer apagar esse grupo?");
        }
    </script>
<?php
        } else {
            echo "<p>Grupo não encontrado.</p>";
        }
    } else {
        echo "<p>ID do grupo não foi informado.</p>";
    }
?>
</body>
</html>
