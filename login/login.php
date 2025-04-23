<?php
require_once("../conexao/conexao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['selecao_login']; 

    $email = mysqli_real_escape_string($conexao, $email);
    $senha = mysqli_real_escape_string($conexao, $senha);

    if ($tipo === 'faculdade') {
        $tabela = 'FACULDADE';
    } elseif ($tipo === 'aluno') {
        $tabela = 'ALUNO';
    } else {
        echo "Tipo de login inválido.";
        exit;
    }

    $sql = "SELECT * FROM $tabela WHERE email = '$email'";
    $resultado = mysqli_query($conexao, $sql);
    $usuario = mysqli_fetch_assoc($resultado);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_start();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['tipo'] = $tipo;
        
        if ($tipo === 'faculdade') {
            $_SESSION['id_faculdade'] = $usuario['id_faculdade']; 
        } elseif ($tipo === 'aluno') {
            $_SESSION['id_aluno'] = $usuario['id_aluno']; 
        }

        if ($tipo === 'faculdade') {
            header("Location: ../menu_faculdade/menu_faculdade.php");
        } elseif ($tipo === 'aluno') {
            header("Location: teste.php");
        }
        exit;
    } else {
        echo "<script>alert('Email ou senha incorretos.');</script>";
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
        <form action="" method="post" onsubmit="return validarRadios()">
            Email: <input class="item texto" type="email" name="email" required> <br>
            Senha:<input class="item texto" type="password" name="senha" required> <br>
            <input class="item radio" type="radio" value="faculdade" name="selecao_login"> Sou instituição<br>
            <input class="item radio" type="radio" value="aluno" name="selecao_login"> Sou aluno<br>
            <button class="entrar" type="submit">Entrar</button>
        </form>
    </div>
</body>
    <script>
        function validarRadios() {
            if (!document.querySelector('input[name="selecao_login"]:checked')) {
                alert('Selecione se você é instituição ou aluno.');
                return false;
            }
            return true;
        }
    </script>
</html>