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
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome_grupo = $_POST['nome_grupo'];
        $email_adm = $_POST['adm'];
        $descricao = $_POST['descricao'];
        $idUsuario = $_SESSION['id_faculdade']; 

        if (empty($nome_grupo)) $erros[] = "O nome do grupo é obrigatório.";
        if (empty($email_adm)) $erros[] = "O email do administrador é obrigatório.";
        if (empty($descricao)) $erros[] = "A descrição é obrigatória.";

        if (empty($erros)) {
            $sql_email_adm = "SELECT * FROM aluno WHERE email = '$email_adm'";
            $result = $conexao->query($sql_email_adm);
            if ($result && $result->num_rows == 0) {
                $erros[] = "Este administrador não tem cadastro de aluno";
                echo "<script>
                    alert('Inclua um email de aluno válido!');
                    window.location.href = '../menu_faculdade/criar_grupo.php';
                </script>";
            }
        }

        if (empty($erros)) {
            $check = "SELECT * FROM grupo WHERE nome = '$nome_grupo'";
            $result = $conexao->query($check);
            if ($result && $result->num_rows > 0) {
                $erros[] = "Você já possui um grupo com este nome.";
                echo "<script>
                    alert('Você já possui um grupo com este nome');
                    window.location.href = '../menu_faculdade/criar_grupo.php';
                </script>";
            }
        }

        if (empty($erros)) {
            $sql_grupo = "INSERT INTO grupo (id_faculdade, nome, descricao) 
                        VALUES ('$idUsuario', '$nome_grupo', '$descricao')";
            if ($conexao->query($sql_grupo)) {
                $id_grupo = mysqli_insert_id($conexao);
                
                $email_adm = mysqli_real_escape_string($conexao, $email_adm);
                $achar_id_aluno = "SELECT id_aluno FROM aluno WHERE email = '$email_adm'";
                $resultado = mysqli_query($conexao, $achar_id_aluno);
        
                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    $linha = mysqli_fetch_assoc($resultado);
                    $id_aluno = $linha['id_aluno'];
                    
                    $inserir_grupo_aluno = "INSERT INTO grupo_aluno (id_grupo, id_aluno, is_adm)
                                            VALUES('$id_grupo', '$id_aluno', 1)";
                    if ($conexao->query($inserir_grupo_aluno)) {
                        echo "<script>alert('Grupo e administrador criados com sucesso.');</script>";
                        exit();
                    } else {
                        $erros[] = "Erro ao adicionar administrador ao grupo: " . $conexao->error;
                    }
                } else {
                    $erros[] = "Administrador não encontrado com esse e-mail.";
                }
            } else {
                $erros[] = "Erro ao cadastrar grupo: " . $conexao->error;
            }
        }

        if(empty($erros)){
            $email_adm = mysqli_real_escape_string($conexao, $email_adm);
            $achar_id_aluno = "SELECT id_aluno FROM aluno WHERE email = '$email_adm'";
            $resultado = mysqli_query($conexao, $achar_id_aluno);

            if ($resultado && mysqli_num_rows($resultado) > 0) {
                $linha = mysqli_fetch_assoc($resultado);
                $id_aluno = $linha['id_aluno'];
            } else {
                $erros[] = "Administrador não encontrado com esse e-mail.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/criar_grupo.css">
    <title>Criar Grupo</title>
</head>
<body>
    <header class="cabecalho">
        <h1 class="cabecalho_titulo">Criar grupo</h1>
    </header>

    <form class="formulario" action="" method="post">
        <div class="formulario_div">
            <input class="campo" type="text" placeholder="Nome do grupo" name="nome_grupo" required>
            <input class="campo" type="email" placeholder="Email Administrador" name="adm" required>
            <input  class="campo" type="text" placeholder="Descrição" name="descricao" required>
            <button  class="botao" type="submit">Criar</button>
        </div>
    </form>
    
</body>
</html>