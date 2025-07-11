<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
require_once("../conexao/conexao.php");
require_once('../verifica_sessao/verifica_sessao.php');

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: ../login/login.php");
    exit;
}

if (!isset($_POST['id_grupo']) || !isset($_POST['mensagem']) || empty(trim($_POST['mensagem']))) {
    header("Location: grupo_detalhes.php?id=" . ($_POST['id_grupo'] ?? ''));
    exit;
}

$id_grupo = $_POST['id_grupo'];
$id_aluno = $_SESSION['id_aluno'];
$mensagem = trim($_POST['mensagem']);

$sql_verifica = "SELECT 1 FROM grupo_aluno WHERE id_grupo = ? AND id_aluno = ?";
$stmt = $conexao->prepare($sql_verifica);
$stmt->bind_param("ii", $id_grupo, $id_aluno);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    header("Location: grupo_detalhes.php?id=$id_grupo");
    exit;
}


$sql_penalizado = "SELECT penalizado FROM grupo_aluno 
                  WHERE id_grupo = ? AND id_aluno = ?";
$stmt = $conexao->prepare($sql_penalizado);
$stmt->bind_param("ii", $id_grupo, $id_aluno);
$stmt->execute();
$resultado = $stmt->get_result();
$dados = $resultado->fetch_assoc();

if ($dados['penalizado'] == 1) {
    $_SESSION['alert_message'] = 'Você está penalizado e não pode postar mensagens neste grupo.';
    header("Location: grupo_detalhes.php?id=$id_grupo");
    exit;
}

$nome_arquivo = null;
if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
    $pasta_upload = '../uploads/';
    if (!is_dir($pasta_upload)) {
        mkdir($pasta_upload, 0755, true);
    }

    $tmp_name = $_FILES['arquivo']['tmp_name'];
    $nome_original = basename($_FILES['arquivo']['name']);
    $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));

    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

    if (!in_array($extensao, $extensoes_permitidas)) {
        die("Tipo de arquivo não permitido. Apenas imagens e PDFs são aceitos.");
    }

    $nome_arquivo = uniqid() . '.' . $extensao;
    $destino = $pasta_upload . $nome_arquivo;

    if (!move_uploaded_file($tmp_name, $destino)) {
        die("Erro ao enviar o arquivo.");
    }
}

$sql_insert = "INSERT INTO forum_geral (id_grupo, id_aluno, mensagem, arquivo, data_postagem) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conexao->prepare($sql_insert);
$stmt->bind_param("iiss", $id_grupo, $id_aluno, $mensagem, $nome_arquivo);
$stmt->execute();

header("Location: grupo_detalhes.php?id=$id_grupo&aba=forumGeral");
exit;
?>
