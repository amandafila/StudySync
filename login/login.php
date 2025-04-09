<?php
require_once("conexao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuario WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $dados = $resultado->fetch_assoc();

        if (password_verify($senha, $dados['senha'])) {
            session_start();
            $_SESSION['email'] = $dados['email'];
            $_SESSION['nome'] = $dados['nome'];
            header("Location: ../menu_aluno/menu_aluno.html");
            exit();
        } else {
            echo "<script>alert('Senha incorreta.');</script>";
        }
    } else {
        echo "<script>alert('Usuário não encontrado');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/login.css">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <div class="quadrado_login">
        <form action="" method="post">
            Email: <input class="item texto" type="email" name="email" required> <br>
            Senha:<input class="item texto" type="password" name="senha" required> <br>
            <input class="item radio" type="radio" value="faculdade" name="selecao_login"> Sou instituição<br>
            <input class="item radio" type="radio" value="faculdade" name="selecao_login"> Sou aluno<br>
            <button class="entrar" type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>