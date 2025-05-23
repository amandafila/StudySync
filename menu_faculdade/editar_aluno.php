<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (isset($_GET['id_aluno'])) {
    $idAluno = intval($_GET['id_aluno']);
} elseif (isset($_GET['id'])) {
    $idAluno = intval($_GET['id']);
} else {
    die("ID de aluno inválido.");
}

$idGrupo = isset($_GET['id_grupo']) ? intval($_GET['id_grupo']) : 0;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $username = trim($_POST['username'] ?? '');
    $nome     = trim($_POST['nome']     ?? '');

    if ($email === '' || $username === '' || $nome === '') {
        $erro = "Todos os campos devem ser preenchidos.";
    } else {
        $sqlUpdate = "UPDATE aluno
                    SET email = ?, username = ?, nome = ?
                    WHERE id_aluno = ?";
        $stmt = $conexao->prepare($sqlUpdate);
        $stmt->bind_param("sssi", $email, $username, $nome, $idAluno);

        if ($stmt->execute()) {

            header("Location: listar_alunos.php?id_grupo=$idGrupo");
            exit;
        } else {
            $erro = "Falha ao atualizar: " . $stmt->error;
        }
    }
}


$sql = "SELECT email, username, nome, cpf FROM aluno WHERE id_aluno = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $idAluno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Aluno não encontrado.");
}

$aluno = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Editar Aluno #<?php echo $idAluno; ?></title>
    <link rel="stylesheet" href="../assets/styles/editar_aluno.css">
</head>
<body>
<header><h1>Editar Aluno #<?php echo $idAluno; ?></h1></header>

<?php if (!empty($erro)): ?>
    <p style="color:red;"><?php echo htmlspecialchars($erro); ?></p>
<?php endif; ?>

<form method="post" action="">
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"
        value="<?php echo htmlspecialchars($aluno['email']); ?>" required><br><br>

    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username"
        value="<?php echo htmlspecialchars($aluno['username']); ?>" required><br><br>

    <label for="nome">Nome:</label><br>
    <input type="text" id="nome" name="nome"
        value="<?php echo htmlspecialchars($aluno['nome']); ?>" required><br><br>

    <label>CPF (não editável):</label><br>
    <input type="text" value="<?php echo htmlspecialchars($aluno['cpf']); ?>" disabled><br><br>

    <button type="submit">Salvar</button>
    <a href="listar_alunos_grupo.php?id_grupo=<?php echo $idGrupo; ?>">
    <button type="button">Cancelar</button>
    </a>
</form>
</body>
</html>