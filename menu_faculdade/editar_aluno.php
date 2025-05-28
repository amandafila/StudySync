<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_GET['id'])) {
    echo "ID do aluno não fornecido.";
    exit;
}

$id_aluno = $_GET['id'];

$sql = "SELECT * FROM aluno WHERE id_aluno = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Aluno não encontrado.";
    exit;
}

$aluno = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $username = $_POST["username"];
    $nome = $_POST["nome"];

    $sql = "UPDATE aluno SET email = ?, username = ?, nome = ? WHERE id_aluno = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssi", $email, $username, $nome, $id_aluno);

    if ($stmt->execute()) {
        echo "<script>
                alert('Aluno atualizado com sucesso!');
                window.location.href = 'listar_alunos.php';
              </script>";
    } else {
        echo "Erro ao atualizar: " . $conexao->error;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/styles/editar_alunos.css">
    <title>Editar Aluno</title>
</head>
<body>
    <?php include('../header/header_facul.php'); ?>
    <h1>Editar Aluno</h1>
    <form method="POST">
        <p><strong>ID:</strong> <?= htmlspecialchars($aluno['id_aluno']) ?></p>
        <p><strong>CPF:</strong> <?= htmlspecialchars($aluno['cpf']) ?></p>

        <label>Email:<br>
            <input type="email" name="email" value="<?= htmlspecialchars($aluno['email']) ?>" required>
        </label><br><br>

        <label>Username:<br>
            <input type="text" name="username" value="<?= htmlspecialchars($aluno['username']) ?>" required>
        </label><br><br>

        <label>Nome:<br>
            <input type="text" name="nome" value="<?= htmlspecialchars($aluno['nome']) ?>" required>
        </label><br><br>

        <button type="submit">Salvar Alterações</button>
        <a href="listar_alunos.php"><button type="button">Cancelar</button></a>
    </form>
</body>
</html>
