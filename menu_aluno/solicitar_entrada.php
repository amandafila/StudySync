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
}else{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome_grupo = $conexao->real_escape_string($_POST['nome_grupo']);
        $mensagem = $conexao->real_escape_string($_POST['mensagem']);
        $id_aluno = $_SESSION['id_aluno'];
    
        $sql = "SELECT id_grupo FROM grupo WHERE nome = '$nome_grupo'";
        $result = $conexao->query($sql);
    
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_grupo = $row['id_grupo'];
    
            // Verifica se já há uma solicitação pendente
            $checkSql = "SELECT * FROM solicitacao_grupo 
                         WHERE id_grupo = $id_grupo AND id_aluno = $id_aluno AND status = 'pendente'";
            $checkResult = $conexao->query($checkSql);
    
            if ($checkResult->num_rows > 0) {
                echo "<script>alert('Você já solicitou entrada neste grupo. Aguarde aprovação.');</script>";
            } else {
                $insertSql = "INSERT INTO solicitacao_grupo (id_grupo, id_aluno, mensagem) 
                              VALUES ($id_grupo, $id_aluno, '$mensagem')";
                if ($conexao->query($insertSql)) {
                    echo "<script>alert('Solicitação enviada com sucesso!');</script>";
                } else {
                    echo "<script>alert('Erro ao enviar solicitação.');</script>";
                }
            }
        } else {
            echo "<script>alert('Este grupo não existe!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="POST">
        <input type="text" name='nome_grupo'required placeholder="Digite o nome do grupo"> <br>
        <input type="text" name='mensagem' required placeholder="Digite uma mensagem"> <br>
        <button type="submit">Solicitar</button>
    </form>
</body>
</html>