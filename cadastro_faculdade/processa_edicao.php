<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function conecta_db() {
    $conn = new mysqli("127.0.0.1", "root", "", "studysync");
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }
    return $conn;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?erro=metodo_invalido");
    exit();
}

$campos_obrigatorios = ['id', 'usuario', 'email', 'cnpj'];
foreach ($campos_obrigatorios as $campo) {
    if (empty($_POST[$campo])) {
        header("Location: update_facul.php?id=".$_POST['id']."&erro=campo_vazio");
        exit();
    }
}

$id = (int)$_POST['id'];
$usuario = htmlspecialchars(strip_tags($_POST['usuario']));
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj']);


$conn = conecta_db();

$stmt = $conn->prepare("UPDATE cadastro_faculdade SET usuario = ?, email = ?, cnpj = ? WHERE id = ?");
$stmt->bind_param("sssi", $usuario, $email, $cnpj, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: index.php?sucesso=atualizado");
    } else {
        header("Location: index.php?aviso=nada_alterado");
    }
} else {
    header("Location: update_facul.php?id=".$id."&erro=banco_dados");
}

$stmt->close();
$conn->close();
exit();
?>