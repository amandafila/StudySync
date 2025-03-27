<?php
function conecta_db() {
    $server = "127.0.0.1";
    $user = "root"; 
    $pass = ""; 
    $db_name = "studysync"; 

    $conexao = new mysqli($server, $user, $pass, $db_name);
    return $conexao;
}

if (isset($_GET['usuario'])) {
    $usuario = $_GET['usuario'];
    $conexao = conecta_db();

    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }

    $stmt = $conexao->prepare("DELETE FROM cadastro_faculdade WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Usuário excluído com sucesso!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir!'); window.location.href='index.php';</script>";
    }

    $stmt->close();
    $conexao->close();
} else {
    echo "<script>alert('Usuário não encontrado!'); window.location.href='index.php';</script>";
}
?>
