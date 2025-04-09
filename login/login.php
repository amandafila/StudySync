<?php
require_once("conexao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
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