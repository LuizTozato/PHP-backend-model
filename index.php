<?php

    //1. ACESSANDO BANCO DE DADOS ===========================
    require_once('db/conexao.php');

    //2. CRIANDO TABELA
    $sql = "CREATE TABLE IF NOT EXISTS tb_clientes (
        id_cliente        INTEGER PRIMARY KEY AUTO_INCREMENT,
        nome              TEXT NOT NULL,
        data_nascimento   TEXT NOT NULL,
        cpf               TEXT NOT NULL,
        celular           TEXT NOT NULL,
        email             TEXT NOT NULL,
        endereco          TEXT NOT NULL,
        observacao        TEXT
    )";

    $pdo->exec($sql);

    //3. PROCESSANDO CORPO DA REQUISIÇÃO POR POST
    $body = file_get_contents('php://input'); //capturar do json da requisição

    $body = json_decode($body);
    
    switch($_SERVER['REQUEST_METHOD']){
        case "POST":
            requestPost($body, $pdo);
            break;
        
        case "GET":
            requestGet($pdo);
            break;
                    
        case "PUT":
            requestPut($body, $pdo);
            break;
        
        case "DELETE":
            requestDelete($body, $pdo);
            break;

    
        default:
            resposta(400, false, 'Metodo Invalido.', '');
                    
    }

    //=======================
    //FUNCOES CRUD
    function requestPost($body, $pdo){

        if($body->id_cliente === ''){

            //CREATE
            //4. INSERINDO DADOS ANTI SQL INJECTION
            $sql = $pdo->prepare("INSERT INTO tb_clientes VALUES (null,?,?,?,?,?,?,?)");

            $sql->execute(array(
                $body->nome,
                $body->data_nascimento,
                $body->cpf,
                $body->celular,
                $body->email,
                $body->endereco,
                $body->observacao
            ));
            
            resposta(200, true, "Cliente cadastrado com sucesso!", '');
        
        } else {
            
            //OBTENDO OS REGISTROS DE 1 ÚNICO CLIENTE POR ID
            //READ
            $sql = $pdo->prepare("SELECT * FROM tb_clientes WHERE id_cliente = ?");
            $sql->execute([$body->id_cliente]);
            $dados = $sql->fetch(PDO::FETCH_OBJ);

            resposta(200, true, "Registro de 1 único cliente lido com sucesso!", $dados);
                        
        }

    }

    function requestGet($pdo){

        $busca = $_GET['busca'];
        $limit = $_GET['limit'];
        $offset = $_GET['offset'];

        if($busca !== ''){

            //READ
            $sql = $pdo->prepare("SELECT * FROM tb_clientes WHERE nome LIKE :nome OR email LIKE :email LIMIT :limit OFFSET :offset");
            $sql->bindValue(":nome", "%$busca%", PDO::PARAM_STR);
            $sql->bindValue(":email", "%$busca%", PDO::PARAM_STR);
            $sql->bindValue(":limit", $limit, PDO::PARAM_INT);
            $sql->bindValue(":offset", $offset, PDO::PARAM_INT);
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_OBJ);

            $sql = $pdo->prepare("SELECT COUNT(id_cliente) AS total FROM tb_clientes WHERE nome LIKE :nome OR email LIKE :email");
            $sql->bindValue(":nome", "%$busca%", PDO::PARAM_STR);
            $sql->bindValue(":email", "%$busca%", PDO::PARAM_STR);
            $sql->execute();
            $total = $sql->fetch(PDO::FETCH_OBJ);
    
            resposta(200, true, "Registros dos clientes lidos com sucesso!", [$dados,$total]);

        } else {

            //READ
            $sql = $pdo->prepare("SELECT * FROM tb_clientes LIMIT :limit OFFSET :offset");
            $sql->bindValue('limit', $limit, PDO::PARAM_INT);
            $sql->bindValue('offset', $offset, PDO::PARAM_INT);
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_OBJ);

            $sql = $pdo->prepare("SELECT COUNT(id_cliente) AS total FROM tb_clientes");
            $sql->execute();
            $total = $sql->fetch(PDO::FETCH_OBJ);
    
            resposta(200, true, "Registros dos clientes lidos com sucesso!", [$dados,$total]);

        }
    }

    function requestPut($body, $pdo){

        //UPDATE
        //4. ATUALIZANDO DADOS ANTI SQL INJECTION
        $sql = $pdo->prepare("UPDATE tb_clientes SET 
            nome = ?,
            data_nascimento = ?,
            cpf = ?,
            celular = ?,
            email = ?,
            endereco = ?,
            observacao = ?
            WHERE id_cliente = ?");

        $sql->execute(array(
            $body->nome,
            $body->data_nascimento,
            $body->cpf,
            $body->celular,
            $body->email,
            $body->endereco,
            $body->observacao,
            $body->id_cliente
        ));
        
        resposta(200, true, "Cliente atualizado com sucesso!", '');
    }

    function requestDelete($body, $pdo){

        //DELETE
        //4. ATUALIZANDO DADOS ANTI SQL INJECTION
        $sql = $pdo->prepare("DELETE FROM tb_clientes 
            WHERE id_cliente = ?");

        $sql->execute([$body->id_cliente]);
        
        resposta(200, true, "Cliente excluído com sucesso!", '');        

    }

?>