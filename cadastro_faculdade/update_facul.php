<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("conexao.php");
$conn = $conexao;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?erro=id_invalido");
    exit();
}

$id_faculdade = (int)$_GET['id'];

$stmt = $conn->prepare("
    SELECT u.username as usuario, u.email, f.cnpj, f.id_usuario 
    FROM faculdade f 
    JOIN usuario u ON f.id_usuario = u.id 
    WHERE f.id_faculdade = ?
");
$stmt->bind_param("i", $id_faculdade);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $conn->close();
    header("Location: index.php?erro=faculdade_nao_encontrada");
    exit();
}

$faculdade = $resultado->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Editar Faculdade</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="card">
    <div class="card-header bg-primary text-white">
        <h4><i class="fa fa-edit"></i> Editar Faculdade (ID: <?= $id_faculdade ?>)</h4>
    </div>
    <div class="card-body">
        <form action="processa_edicao.php" method="POST">
        <input type="hidden" name="id_faculdade" value="<?= $id_faculdade ?>">
        <input type="hidden" name="id_usuario" value="<?= $faculdade['id_usuario'] ?>">

        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="usuario" name="usuario" 
                value="<?= htmlspecialchars($faculdade['usuario']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" 
                value="<?= htmlspecialchars($faculdade['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="cnpj" class="form-label">CNPJ</label>
            <input type="text" class="form-control" id="cnpj" name="cnpj" 
                value="<?= htmlspecialchars($faculdade['cnpj']) ?>" required>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="index.php" class="btn btn-secondary me-md-2">
                <i class="fa fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Salvar Alterações
            </button>
        </div>
        </form>
    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
