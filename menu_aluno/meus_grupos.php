<?php
require_once("../conexao/conexao.php");
session_start();

$erros = [];

if (!isset($_SESSION['id_aluno'])) {
    echo "Erro: Não há um aluno associado à sua sessão. Verifique o login.";
    exit;
}

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    echo "<script>
        alert('Você não está logado!');
        window.location.href = '../login/login.php';
    </script>";
    exit;
}

$id_aluno = $_SESSION['id_aluno'];


$sql = "SELECT g.id_grupo, g.nome as nome_grupo, g.descricao 
        FROM grupo_aluno ga
        JOIN grupo g ON ga.id_grupo = g.id_grupo
        WHERE ga.id_aluno = ?";
        
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_aluno);
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
    <link rel="stylesheet" href="../assets/styles/meus_grupos.css">
    <title>Meus Grupos</title>
</head>
<body>
    <?php include('../header/header_aluno.php'); ?>
    
    <main>
        <h1>Meus Grupos</h1>
        
        <?php if (count($grupos) > 0): ?>
            <div class="grupos-container">
                <?php foreach ($grupos as $grupo): ?>
                    <div class="grupo-card">
                        <h2><?php echo htmlspecialchars($grupo['nome_grupo']); ?></h2>
                        <a class="botaoacessar" href="grupo_detalhes.php?id=<?php echo $grupo['id_grupo']; ?>">Acessar Grupo</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Você não está cadastrado em nenhum grupo ainda.</p>
        <?php endif; ?>
    </main>
</body>
</html>