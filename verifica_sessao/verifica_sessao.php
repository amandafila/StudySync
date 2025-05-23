<?php
    session_start();

    $tempo_maximo = 900;

    if (isset($_SESSION['ultimo_acesso'])) {
        if (time() - $_SESSION['ultimo_acesso'] > $tempo_maximo) {
            session_unset();
            session_destroy();
            echo "<script>
                alert('Você não está logado!');
                window.location.href = '../login/login.php';
            </script>";
            exit();
        }
    }
    $_SESSION['ultimo_acesso'] = time();

?>