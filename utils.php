<?php

    //FUNCOES AUXILIARES ==================
    function resposta($codigo, $ok, $msg, $dados){

        http_response_code($codigo);
        echo (json_encode([
            'ok' => $ok,
            'msg' => $msg,
            'dados' => $dados
        ]));

        die;
    };

?>