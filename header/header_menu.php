<!DOCTYPE html>
<html>
<head>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            overflow-x: hidden;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5vh 5vw;
            color: white;
            height: 14vh;
            font-family: "Montserrat";
            font-size: 3vh;
            font-weight: 550;
            background: linear-gradient(to bottom, rgba(22, 13, 4, 0.95), rgba(32, 20, 7, 0.3), rgba(32, 20, 7, 0));
            position: relative;
            width: 100vw;
        }

        header a {
            text-decoration: none;
            color: white;
            font-family: "Montserrat";
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .menu-container {
            position: relative;
            display: inline-block;
            margin-left: 2vw;
        }

        .menu_aluno,
        .menu_login {
            font-weight: 200;
            font-size: 2.5vh;
            cursor: pointer;
            white-space: nowrap;
            padding: 8px 12px;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: -5vw;
            background-color: rgba(21, 13, 4, 0.9);
            min-width: 180px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 2vh;
            font-weight: 200;
            white-space: nowrap;
        }

        .dropdown-content a:hover {
            background-color: rgba(11, 7, 2, 0.7);
        }

        .menu-container:hover .dropdown-content {
            display: block;
        }

        .right-buttons {
            display: flex;
            align-items: center;
        }

    </style>
</head>
<body>
    <header> 
        <a class="esquerda" href="../menu/index.php">StudySync</a> 
        <div class="right-buttons">

            <div class="menu-container">
                <a href="#" class="menu_aluno">Cadastrar</a>
                <div class="dropdown-content">
                    <a href="../cadastro_faculdade/cadastro_facul.php">Faculdade</a>
                    <a href="../cadastro_aluno/cadastro_aluno.php">Aluno</a>
                </div>
            </div>


            <div class="menu-container">
                <a href="../login/login.php" class="menu_login">Login</a>
            </div>
        </div>
    </header>
</body>
</html>
