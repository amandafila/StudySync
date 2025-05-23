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
    require_once('../verifica_sessao/verifica_sessao.php');

    $erros = [];

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
    <?php include('../header/header_facul.php'); ?>

    <div class="div_corpo">
        <div class="div_corpo_div">
            <div class="informacoes">
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($grupo['nome_grupo']); ?></p>
                    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($grupo['descricao']); ?></p>
                    <p><strong>Administrador:</strong> <?php echo htmlspecialchars($grupo['username']); ?></p>
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
