<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

$erros = [];

$faculdades_result = $conexao->query("SELECT nome FROM faculdade ORDER BY nome");
if (!$faculdades_result) {
    die("Erro ao buscar faculdades: " . $conexao->error);
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $empresa = $_POST['empresa'];
    $descricao = $_POST['descricao'];
    $requisitos = $_POST['requisitos'];
    $localizacao = $_POST['localizacao'];
    $facaulde = $_POST['faculdade'];
    $link = $_POST['link']; 

    $stmt = $conexao->prepare("INSERT INTO vagas (titulo, empresa, descricao, requisitos, localizacao, link, faculdade) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $titulo, $empresa, $descricao, $requisitos, $localizacao, $link, $facaulde);
    $stmt->execute();

    echo "<script>alert('Vaga criada com sucesso');</script>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/criar_vaga.css">
    <title>Criar Vagas</title>
</head>
<body>
    <form method="POST">
        <?php include('../header/header_facul.php'); ?>
        <div class="formulario">
            <div class="formulario_div">
                <h1>Criar vaga</h1>
                <label>Título da vaga*:</label>
                <input class="campo" type="text" name="titulo" required>

                <label>Empresa*:</label>
                <input class="campo" type="text" name="empresa" required>

                <label>Descrição*:</label>
                <textarea class="campo" name="descricao" required></textarea>

                <label>Requisitos:</label>
                <textarea class="campo" name="requisitos"></textarea>

                <label>Localização:</label>
                <input class="campo" type="text" name="localizacao">

                <select name="faculdade" class="campo" required>
                    <option value="">Selecione...</option>
                    <?php while ($fac = $faculdades_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($fac['nome']) ?>">
                        <?= htmlspecialchars($fac['nome']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>

                <label>Link para inscrição*:</label>
                <input class="campo" type="url" name="link" placeholder="https://exemplo.com" required>

                <button class="botao" type="submit">Criar Vaga</button>
            </div>
        </div>
    </form>
</body>
</html>
