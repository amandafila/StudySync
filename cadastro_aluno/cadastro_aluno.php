<?php
require_once("../conexao/conexao.php");

$faculdades_result = $conexao->query("SELECT nome FROM faculdade ORDER BY nome");
if (!$faculdades_result) {
    die("Erro ao buscar faculdades: " . $conexao->error);
}

$erros = [];

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

    if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
        $erros[] = "Email inválido.";
    }

    if (!preg_match('/^[a-zA-Z0-9_]{3,15}$/', $usuario)) {
        $erros[] = "O usuário deve conter apenas letras, números ou '_' e ter entre 3 e 15 caracteres.";
    }

    if (empty($erros)) {
        $check = "SELECT * FROM aluno WHERE username = '$usuario' OR email = '$email' OR cpf = '$cpf'";
        $result = $conexao->query($check);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['username'] === $usuario) {
                    $erros[] = "Usuário já cadastrado.";
                }
                if ($row['email'] === $email) {
                    $erros[] = "Email já cadastrado.";
                }
                if ($row['cpf'] === $cpf) {
                    $erros[] = "CPF já cadastrado.";
                }
            }
        }
    }

    if (empty($erros)) {
        $chave_recuperacao = bin2hex(random_bytes(16));
        $senha = password_hash($senha_raw, PASSWORD_DEFAULT);

        $stmt = $conexao->prepare("INSERT INTO aluno (nome, username, email, senha, cpf, faculdade, chave_recuperacao_hash) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nome, $usuario, $email, $senha, $cpf, $faculdade, $chave_recuperacao);

        if ($stmt->execute()) {
            $id_aluno = $conexao->insert_id;

            session_start();

            $sql = "SELECT * FROM aluno WHERE email = '$email'";
            $resultado = mysqli_query($conexao, $sql);
            $usuario = mysqli_fetch_assoc($resultado);

            $_SESSION['usuario'] = $usuario;
            $_SESSION['tipo'] = 'aluno';
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['id_aluno'] = $id_aluno;

            echo "<script>
                alert('Cadastro realizado com sucesso!\\nSua chave de recuperação é: $chave_recuperacao\\nAnote-a agora, ela não será exibida novamente.');
                window.location.href = '../menu_aluno/meus_grupos.php';
            </script>";
            exit();
        } else {
            $erros[] = "Erro ao cadastrar aluno: " . $stmt->error;
        }
        $stmt->close();
    }

    if (!empty($erros)) {
        $msg = implode("\\n", $erros);
        echo "<script>alert('$msg');</script>";
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
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
      <form method="POST" action="" enctype="multipart/form-data" id="formCadastro">
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">Nome*</p></div>
          <input type="text" name="nome" class="form-control" required />
        </div>
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">Usuário*</p></div>
          <input type="text" name="usuario" class="form-control" required />
        </div>
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">CPF*</p></div>
          <input placeholder="000.000.000-00" type="text" name="cpf" class="form-control" required />
        </div>
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">Email*</p></div>
          <input type="email" name="email" class="form-control" required />
        </div>
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">Faculdade*</p></div>
          <select name="faculdade" class="form-control" required>
            <option value="">Selecione...</option>
            <?php
              $faculdades_result->data_seek(0);
              while ($fac = $faculdades_result->fetch_assoc()):
            ?>
            <option value="<?= htmlspecialchars($fac['nome']) ?>"><?= htmlspecialchars($fac['nome']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-2">
          <div class="div_paragrafo"><p class="paragros_left">Senha*</p></div>
          <input
            placeholder="8 dígitos e no mínimo 1 caractere especial"
            type="password"
            name="senha"
            class="form-control"
            required
          />
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
      cpfInput.addEventListener("input", function () {
        let valor = cpfInput.value.replace(/\D/g, "");
        valor = valor.slice(0, 11);
        valor = valor
          .replace(/(\d{3})(\d)/, "$1.$2")
          .replace(/(\d{3})(\d)/, "$1.$2")
          .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        cpfInput.value = valor;
      });

      const form = document.getElementById("formCadastro");
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

        if (senha.length < 8 || !/[!@#$%^&*(),.?\":{}|<>]/.test(senha)) {
          erros.push("A senha deve ter pelo menos 8 caracteres e conter pelo menos um caractere especial.");
        }

        const regexUsuario = /^[a-zA-Z0-9_]{3,15}$/;
        if (!regexUsuario.test(usuario)) {
          erros.push(
            "O usuário deve conter apenas letras, números ou '_' e ter entre 3 e 15 caracteres."
          );
        }

        if (erros.length > 0) {
          e.preventDefault();
          alert(erros.join("\n"));
        }
      });
    });
  </script>
</body>
</html>
