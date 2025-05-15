<?php
    require_once("../conexao/conexao.php");
    session_start();

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

    $id_faculdade = $_SESSION['id_faculdade'];

    $sql = "SELECT id_grupo, nome AS nome_grupo, descricao 
            FROM grupo 
            WHERE id_faculdade = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_faculdade);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if (!$resultado) {
        die("Erro na consulta: " . $conexao->error);
    }

    $grupos = $resultado->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/visualizar_grupos.css">
    <title>Meus Grupos</title>
</head>
<body>
    <?php include('../header/header_facul.php'); ?>
    
    <main>
        <h1>Grupos Cadastrados</h1>
        <?php if (count($grupos) > 0): ?>
            <div class="grupos-container">
                <?php foreach ($grupos as $grupo): ?>
                    <div class="grupo-card">
                        <h2><?php echo htmlspecialchars($grupo['nome_grupo']); ?></h2>
                        <form action="grupo.php" method="get">
                            <input type="hidden" name="id_grupo" value="<?php echo $grupo['id_grupo']; ?>">
                            <button class="botaoacessar" type="submit">Acessar Grupo</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Você não possui nenhum grupo ainda.</p>
        <?php endif; ?>
    </main>
</body>
</html>