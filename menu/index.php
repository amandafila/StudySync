<?php
    session_start();
    session_unset(); 
    session_destroy(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/index.css">
    <title>StudySync</title>
</head>
<body>
    <div class="nav-bar">
            <a class="study" href="">StudySync</a>
        <div class="nav-options">
            <a href="../cadastro_faculdade/cadastro_facul.php" class="options">Instituição</a>
            <a href="../cadastro_aluno/cadastro_aluno.php" class="options">Aluno</a>
        </div>
    </div>
    <div class="main_title">
        <h1>Sync.</h1>
    </div>
    <footer>
        <p>Desenvolvido por Amanda, Carlos e Arthur</p>
    </footer>
</body>
</html>