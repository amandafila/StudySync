<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/styles/vizu_grupo.css" rel="stylesheet">
    <title>Visualização de grupos</title>
</head>
<body>
<header>
    <div class='titulos_header'>
        <a href=''>StudySync</a>
        <p>Teste</p>
    </div>
</header>

    <h1>Visualizar grupos</h1>
</body>
</html>
<?php
require_once("../conexao/conexao.php");
$sql = "SELECT id_grupo, nome FROM grupo";
$resultado = $conexao->query($sql);

if (!$resultado) {
    die("Erro na query: " . $conexao->error);
}

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $idGrupo = $row['id_grupo'];
        $nomeGrupo = htmlspecialchars($row['nome']);
        echo "
            <form action='grupo.php' method='get' style='display:inline; margin:5px;'>
                <input type='hidden' name='id_grupo' value='" . $idGrupo . "'>
                <button class='grupos' type='submit'>$nomeGrupo</button>
            </form>
        ";
    }
} else {
    echo "Nenhum grupo encontrado.";
}
?>

