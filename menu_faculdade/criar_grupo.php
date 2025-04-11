<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'faculdade') {
    echo "<script>
        alert('Você não está logado!');
        window.location.href = '../login/login.php';
    </script>";
    exit;
}else{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome_grupo = $_POST['nome_grupo'];
        $email_adm = $_POST['adm'];
        $descicao = $_POST['descricao'];

        
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <form action="" method="post">
        <input type="text" placeholder="Nome do grupo" name="nome_grupo" required><br>
        <input type="email" placeholder="Email Administrador" name="adm" required> <br>
        <input type="text" placeholder="Descrição" name="descricao" required>
        <button type="submit">Criar</button>
    </form>
</head>
<body>
    
</body>
</html>