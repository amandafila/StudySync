<?php
require_once("../conexao/conexao.php");
if (isset($_GET['id_grupo'])) {
    $idGrupo = intval($_GET['id_grupo']); 

    if ($conexao->connect_error) {
        die("Erro de conexão: " . $conexao->connect_error);
    }

    $sql = "SELECT * FROM grupo WHERE id_grupo = $idGrupo";
    $resultado = $conexao->query($sql);

    if ($resultado->num_rows > 0) {
        $grupo = $resultado->fetch_assoc();
        echo "<header class='cabecalho'><h1>" . htmlspecialchars($grupo['nome']) . "</h1> </header>";
        echo "<div class='div_corpo'> <div class='div_corpo_div'><p class='descricao'>Descrição: " . htmlspecialchars($grupo['descricao']) . "</p>";
        echo "<button>Excluir grupo</button>";
        echo "<button>Listar alunos</button> </div></div>";
    } else {
        echo "Grupo não encontrado.";
    }

    $conexao->close();
} else {
    echo "ID do grupo não foi fornecido.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/grupo.css">
    <title>Página do grupo</title>
</head>
<body>
    
</body>
</html>