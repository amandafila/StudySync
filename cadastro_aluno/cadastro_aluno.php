<?php
require_once("../conexao/conexao.php");

$faculdades_result = $conexao->query("SELECT nome FROM faculdade ORDER BY nome");
if (!$faculdades_result) {
    die("Erro ao buscar faculdades: " . $conexao->error);
}

$erros = [];
$exibir_chave = false; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $senha_raw = $_POST['senha'];
    $faculdade = trim($_POST['faculdade']);

    if (empty($nome)) $erros[] = "O nome é obrigatório.";
    if (empty($cpf)) $erros[] = "O CPF é obrigatório.";
    if (empty($usuario)) $erros[] = "O usuário é obrigatório.";
    if (empty($email)) $erros[] = "O email é obrigatório.";
    if (empty($faculdade)) $erros[] = "A faculdade é obrigatória.";

    if (empty($senha_raw)) {
        $erros[] = "O campo Senha é obrigatório.";
    } else {
        if (strlen($senha_raw) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $senha_raw)) {
            $erros[] = "A senha deve ter no mínimo 8 caracteres e conter pelo menos um caractere especial.";
        }
    }

    if (!preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf)) {
        $erros[] = "CPF inválido. Use o formato 999.999.999-99.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido.";
    }

    if (!preg_match('/^[a-zA-Z0-9_]{3,15}$/', $usuario)) {
        $erros[] = "O usuário deve conter apenas letras, números ou '_' e ter entre 3 e 15 caracteres.";
    }

    if (empty($erros)) {
        $stmt_check = $conexao->prepare("SELECT username, email, cpf FROM aluno WHERE username = ? OR email = ? OR cpf = ?");
        $stmt_check->bind_param("sss", $usuario, $email, $cpf);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['username'] === $usuario) $erros[] = "Usuário já cadastrado.";
                if ($row['email'] === $email) $erros[] = "Email já cadastrado.";
                if ($row['cpf'] === $cpf) $erros[] = "CPF já cadastrado.";
            }
        }
        $stmt_check->close();
    }

    if (empty($erros)) {
        $chave_recuperacao = bin2hex(random_bytes(16));
        $chave_recuperacao_hash = password_hash($chave_recuperacao, PASSWORD_DEFAULT);
        $senha = password_hash($senha_raw, PASSWORD_DEFAULT);

        $stmt = $conexao->prepare("INSERT INTO aluno (nome, username, email, senha, cpf, faculdade, chave_recuperacao_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nome, $usuario, $email, $senha, $cpf, $faculdade, $chave_recuperacao_hash);

        if ($stmt->execute()) {
            $id_aluno = $conexao->insert_id; 

            session_start();
            
            $sql_busca = "SELECT * FROM aluno WHERE email = ?";
            $stmt_busca = $conexao->prepare($sql_busca);
            
            if ($stmt_busca === false) {
                 die("Erro fatal: Não foi possível preparar a consulta para buscar o usuário recém-criado. Erro: " . $conexao->error);
            }
            
            $stmt_busca->bind_param("s", $email);
            $stmt_busca->execute();
            $usuario_data = $stmt_busca->get_result()->fetch_assoc();
            $stmt_busca->close();

            $_SESSION['usuario'] = $usuario_data;
            $_SESSION['tipo'] = 'aluno';
            $_SESSION['nome'] = $usuario_data['nome'];
            $_SESSION['id_aluno'] = $id_aluno;
            
            $exibir_chave = true; 
        } else {
            $erros[] = "Erro ao cadastrar aluno: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Cadastro de Estudante</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="../assets/styles/cadastro_aluno.css" rel="stylesheet" />
  <style>
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
    .chave-recuperacao button {
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 1rem;
        font-size: 1rem;
    }
    .chave-recuperacao button:hover {
        background-color: #0056b3;
    }
  </style>
</head>
<body>

  <?php if ($exibir_chave): ?>
    <div class="overlay"></div>
    <div class="chave-recuperacao">
      <h3>CADASTRO REALIZADO COM SUCESSO!</h3>
      <p>Guarde sua chave de recuperação em um local seguro:</p>
      <div class="chave"><?php echo htmlspecialchars($chave_recuperacao); ?></div>
      <p>Esta chave será necessária caso você esqueça sua senha.</p>
      <p><strong>Anote agora, pois ela não será exibida novamente.</strong></p>
      <p>Redirecionando para a sua área em 15 segundos...</p>
      <button onclick="window.location.href='../menu_aluno/meus_grupos.php'">Acessar Meus Grupos Agora</button>
    </div>
    <script>
      setTimeout(function() {
        window.location.href = '../menu_aluno/meus_grupos.php';
      }, 15000); 
    </script>
  <?php endif; ?>

  <?php if (!empty($erros)): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        alert("<?php echo implode("\\n", array_map('addslashes', $erros)); ?>");
      });
    </script>
  <?php endif; ?>

  <div class="left">
    <div class="div_left_title">
      <a href="../login/login.php" class="title_left">Login</a>
    </div>
    <div class="div_left_paragraph">
      <p class="left_paragraph">De estudantes, <br /> para estudantes.</p>
    </div>
  </div>

  <div class="right">
    <h2 class="subtitulo_cadastro">Faça já seu cadastro</h2>
    <div class="col-6">
      <form method="POST" action="" id="formCadastro">
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">Nome*</p></div>
          <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" />
        </div>
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">Usuário*</p></div>
          <input type="text" name="usuario" class="form-control" required value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" />
        </div>
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">CPF*</p></div>
          <input placeholder="000.000.000-00" type="text" name="cpf" class="form-control" required value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>" />
        </div>
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">Email*</p></div>
          <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        </div>
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">Faculdade*</p></div>
          <select name="faculdade" class="form-control" required>
            <option value="">Selecione...</option>
            <?php
              $faculdade_selecionada = $_POST['faculdade'] ?? '';
              $faculdades_result->data_seek(0);
              while ($fac = $faculdades_result->fetch_assoc()):
                $nome_fac = htmlspecialchars($fac['nome']);
                $selected = ($nome_fac == $faculdade_selecionada) ? 'selected' : '';
            ?>
            <option value="<?= $nome_fac ?>" <?= $selected ?>><?= $nome_fac ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">Senha*</p></div>
          <input placeholder="8 dígitos e no mínimo 1 caractere especial" type="password" name="senha" class="form-control" required />
        </div>
        <div class="mb-2">
          <button type="submit" class="mt-2 btn btn-primary">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const cpfInput = document.querySelector('input[name="cpf"]');
    if(cpfInput) {
      cpfInput.addEventListener("input", function () {
        let valor = cpfInput.value.replace(/\D/g, "");
        valor = valor.slice(0, 11);
        valor = valor
          .replace(/(\d{3})(\d)/, "$1.$2")
          .replace(/(\d{3})(\d)/, "$1.$2")
          .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        cpfInput.value = valor;
      });
    }

    const form = document.getElementById("formCadastro");
    if(form) {
      form.addEventListener("submit", function (e) {
        const cpf = document.querySelector('input[name="cpf"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const senha = document.querySelector('input[name="senha"]').value;
        const usuario = document.querySelector('input[name="usuario"]').value;
        let erros = [];

        const regexCPF = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
        if (!regexCPF.test(cpf)) {
          erros.push("CPF inválido. Use o formato 999.999.999-99.");
        }

        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexEmail.test(email)) {
          erros.push("Email inválido.");
        }

        if (senha.length < 8 || !/[!@#$%^&*(),.?":{}|<>]/.test(senha)) {
          erros.push("A senha deve ter pelo menos 8 caracteres e conter pelo menos um caractere especial.");
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
    }
  });
  </script>
</body>
</html>