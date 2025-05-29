<?php
require_once("../conexao/conexao.php");

$erros = [];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $cnpj = trim($_POST['cnpj']);
    $cep = trim($_POST['cep']);
    $telefone = trim($_POST['telefone']);
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $senha_raw = $_POST['senha'];

    if (empty($nome)) $erros[] = "O campo Nome é obrigatório.";
    if (empty($cnpj)) $erros[] = "O campo CNPJ é obrigatório.";
    if (empty($cep)) $erros[] = "O campo CEP é obrigatório.";
    if (empty($telefone)) $erros[] = "O campo Telefone é obrigatório.";
    if (empty($usuario)) $erros[] = "O campo Usuário é obrigatório.";
    if (empty($email)) $erros[] = "O campo Email é obrigatório.";
    if (empty($senha_raw)) {
        $erros[] = "O campo Senha é obrigatório.";
    } else {
        if (strlen($senha_raw) < 8) {
            $erros[] = "A senha deve ter no mínimo 8 caracteres.";
        }
        if (strpos($senha_raw, '@') === false) {
            $erros[] = "A senha deve conter pelo menos um caractere '@'.";
        }
    }

    if (empty($erros)) {
        $check = "SELECT * FROM faculdade WHERE username = '$usuario' OR email = '$email'";
        $result = $conexao->query($check);
        if ($result && $result->num_rows > 0) {
            $erros[] = "Usuário ou email já cadastrado.";
        }
    }
    if (empty($erros)) {
        $checar = "SELECT * FROM faculdade WHERE cnpj = '$cnpj'";
        $resultado = $conexao->query($checar);
        if ($resultado && $resultado->num_rows > 0) {
            $erros[] = "CNPJ ja cadastrado";
        }
    }
    if (empty($erros)) {
        $checar2 = "SELECT * FROM faculdade WHERE cep = '$cep'";
        $resultado2 = $conexao->query($checar2);
        if ($resultado2 && $resultado2->num_rows > 0) {
            $erros[] = "CEP ja cadastrado";
        }
    }

    if (empty($erros)) {
        $chave_recuperacao = bin2hex(random_bytes(16));
        $chave_recuperacao_hash = password_hash($chave_recuperacao, PASSWORD_DEFAULT);
        $senha = password_hash($senha_raw, PASSWORD_DEFAULT);
        $sql_faculdade = "INSERT INTO faculdade (nome, username, email, senha, cnpj, cep, telefone, chave_recuperacao_hash) 
                          VALUES ('$nome', '$usuario', '$email', '$senha', '$cnpj', '$cep', '$telefone', '$chave_recuperacao_hash')";
        if ($conexao->query($sql_faculdade)) {
            $exibir_chave = true; 
        } else {
            $erros[] = "Erro ao cadastrar faculdade: " . $conexao->error;
        }
    }
}
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cnpjInput = document.querySelector('input[name="cnpj"]');
    cnpjInput.addEventListener('input', function () {
        let valor = cnpjInput.value.replace(/\D/g, '');
        valor = valor.slice(0, 14);
        valor = valor.replace(/^(\d{2})(\d)/, '$1.$2')
                     .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                     .replace(/\.(\d{3})(\d)/, '.$1/$2')
                     .replace(/(\d{4})(\d)/, '$1-$2');
        cnpjInput.value = valor;
    });

    const cepInput = document.querySelector('input[name="cep"]');
    cepInput.addEventListener('input', function () {
        let valor = cepInput.value.replace(/\D/g, '');
        valor = valor.slice(0, 8);
        valor = valor.replace(/^(\d{5})(\d)/, '$1-$2');
        cepInput.value = valor;
    });

    const telInput = document.querySelector('input[name="telefone"]');
    telInput.addEventListener('input', function () {
        let valor = telInput.value.replace(/\D/g, '');
        if (valor.length > 11) valor = valor.slice(0, 11);
        
        if (valor.length > 2) {
            valor = valor.replace(/^(\d{2})/, '($1) ');
            if (valor.length > 10) {
                valor = valor.replace(/(\d{5})(\d)/, '$1-$2');
            } else {
                valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
            }
        }
        telInput.value = valor;
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        const cnpj = document.querySelector('input[name="cnpj"]').value;
        const cep = document.querySelector('input[name="cep"]').value;
        const telefone = document.querySelector('input[name="telefone"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const senha = document.querySelector('input[name="senha"]').value;
        const usuario = document.querySelector('input[name="usuario"]').value;

        let erros = [];

        const regexCNPJ = /^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/;
        if (!regexCNPJ.test(cnpj)) {
            erros.push("CNPJ inválido. Use o formato 99.999.999/9999-99.");
        }

        const regexCEP = /^\d{5}-\d{3}$/;
        if (!regexCEP.test(cep)) {
            erros.push("CEP inválido. Use o formato 99999-999.");
        }

        const regexTel = /^\(\d{2}\) \d{4,5}-\d{4}$/;
        if (!regexTel.test(telefone)) {
            erros.push("Telefone inválido. Use o formato (99) 99999-9999 ou (99) 9999-9999.");
        }

        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexEmail.test(email)) {
            erros.push("Email inválido.");
        }

        if (senha.length < 8 || !senha.includes('@')) {
            erros.push("A senha deve ter pelo menos 8 caracteres e conter '@'.");
        }

        const regexUsuario = /^[a-zA-Z0-9_]{3,15}$/;
        if (!regexUsuario.test(usuario)) {
            erros.push("O usuário deve conter apenas letras, números ou '_' e ter entre 3 e 15 caracteres.");
        }

        if (erros.length > 0) {
            e.preventDefault();
            alert(erros.join("\n"));
        }
    });
});
</script>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Cadastro de Faculdade</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../assets/styles/cadastro_facul.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Encode+Sans+Expanded:wght@100;200;300;400;500;600;700;800;900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap');

    .chave-recuperacao {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0,0,0,0.2);
        z-index: 1000;
        text-align: center;
        max-width: 500px;
        width: 90%;
    }

    .chave-recuperacao .chave {
        font-family: monospace;
        font-size: 1.5rem;
        font-weight: bold;
        background: #f8f9fa;
        padding: 1rem;
        margin: 1rem 0;
        border-radius: 4px;
        word-break: break-all;
    }

    .chave-recuperacao h3 {
        color: #343a40;
        margin-bottom: 1rem;
    }

    .chave-recuperacao p {
        margin: 0.5rem 0;
        color: #555;
    }
    
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        z-index: 999;
    }
</style>
</head>

<body>
  <?php if (isset($exibir_chave) && $exibir_chave): ?>
    <div class="overlay"></div>
    <div class="chave-recuperacao">
      <h3>SUA CHAVE DE RECUPERAÇÃO (GUARDE EM LOCAL SEGURO):</h3>
      <div class="chave"><?php echo $chave_recuperacao; ?></div>
      <p>Esta chave será necessária caso você esqueça sua senha.</p>
      <p>Anote agora, pois ela não será exibida novamente.</p>
      <p>Redirecionando para a página de login em 30 segundos...</p>
      <button onclick="window.location.href='../login/login.php'" class="btn btn-primary">Ir para Login Agora</button>
    </div>
    <script>
      setTimeout(function() {
        window.location.href = '../login/login.php';
      }, 30000);
    </script>
  <?php endif; ?>

  <div class="left">
    <div class="div_left_title">
      <a href="../login/login.php"class="title_left">Login</a>
    </div>
    <div class="div_left_paragraph">
      <p class="left_paragraph">Faça parte dessa <br> comunidade <br> incrível.</p>
  
    </div>
  </div>

  <div class="right">
    <div class="div_header">
      <div class="header_login">
      </div>
    </div>

    <h2 class="subtitulo_cadastro">Cadastre a sua instituição</h2>

    <?php if (!empty($erros)): ?>
      <div style="color: red; margin-bottom: 10px;">
        <ul>
          <?php foreach ($erros as $erro): ?>
            <li><?= htmlspecialchars($erro) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="col-6">
      <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-2">
          <p class="paragros_left">Nome*</p>
          <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">CNPJ*</p>
          <input type="text" name="cnpj" class="form-control" placeholder="99.999.999/9999-99" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">CEP*</p>
          <input type="text" name="cep" class="form-control" placeholder="99999-999" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">Telefone*</p>
          <input type="text" name="telefone" class="form-control" placeholder="(99) 99999-9999" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">Usuário*</p>
          <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">Email*</p>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-2">
          <p class="paragros_left">Senha*</p>
          <input type="password" name="senha" class="form-control" placeholder="Mínimo 8 caracteres com @" required>
        </div>
        <div class="mb-2">
          <button type="submit" class="mt-2 btn btn-primary">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>