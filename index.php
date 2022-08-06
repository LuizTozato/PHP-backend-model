<?php

    //CORS config
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");  

    //FUNCOES AUXILIARES
    function resposta($codigo, $ok, $msg){

        http_response_code($codigo);
        echo (json_encode([
            'ok' => $ok,
            'msg' => $msg
        ]));

        die;
    };

    //1. ACESSANDO BANCO DE DADOS ===========================
    require('db/conexao.php');

    //2. CRIANDO TABELA
    $sql = "CREATE TABLE IF NOT EXISTS tb_clientes (
        id_cliente        INTEGER PRIMARY KEY AUTO_INCREMENT,
        nome              TEXT NOT NULL,
        data_nascimento   TEXT NOT NULL,
        cpf               TEXT NOT NULL,
        celular           TEXT,
        email             TEXT,
        endereco          TEXT,
        observacao        TEXT
    )";

    $pdo->exec($sql);

    //REQUISIÇÃO VINDA POR OPTIONS
    if($_SERVER['REQUEST_METHOD'] === "OPTIONS"){
        resposta(200, true, '');
    }


    //REQUISICAO VINDA POR POST
    if($_SERVER['REQUEST_METHOD'] !== "POST"){
        
        resposta(400, false, 'Metodo Invalido. Diferente de POST');

    } else {

        //=======================
        //3. PROCESSANDO CORPO DA REQUISIÇÃO
        $body = file_get_contents('php://input'); //capturar do json da requisição
        if(!$body){
            resposta(400, false, "Corpo da requisicao nao encontrado");
        }

        $body = json_decode($body);

        $body->nome = filter_var($body->nome, FILTER_SANITIZE_STRING);
        $body->data_nascimento = filter_var($body->data_nascimento, FILTER_SANITIZE_STRING);
        $body->cpf = filter_var($body->cpf, FILTER_SANITIZE_STRING);
        $body->celular = filter_var($body->celular, FILTER_SANITIZE_STRING);
        $body->email = filter_var($body->email, FILTER_SANITIZE_STRING);
        $body->endereco = filter_var($body->endereco, FILTER_SANITIZE_STRING);
        $body->observacao = filter_var($body->observacao, FILTER_SANITIZE_STRING);
        
        //controle de input inválido
        if(!$body->nome || !$body->data_nascimento || !$body->cpf){
            resposta(400, false, "Dados Invalidos");
        }

        //=======================
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
        
        resposta(200, true, "Cliente cadastrado com sucesso!");

    };


?>