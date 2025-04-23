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
        echo "<h1>" . htmlspecialchars($grupo['nome']) . "</h1>";
        echo "<p>Descrição: " . htmlspecialchars($grupo['descricao']) . "</p>";
        echo "<button>Excluir grupo</button>";
        echo "<button>Listar alunos</button>";
    } else {
        echo "Grupo não encontrado.";
    }

    $conexao->close();
} else {
    echo "ID do grupo não foi fornecido.";
}
?>