<?php

require_once 'CLASSES/Class.crud.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Content-Type: application/json; charset=UTF-8');

$uri = basename($_SERVER['REQUEST_URI']);


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(is_numeric($uri)){
        Crud::setTabela('estoque');
        $dados = Crud::select(
                 'SELECT * FROM estoque WHERE ID = :ID',
                 ['ID' => $uri],
                 TRUE
             );
             if ($dados == []) {
                http_response_code(404);
                echo json_encode(['mensagem' => 'id não encontrado']);
             }else {
                 echo json_encode($dados[0]);
                 http_response_code(200);
               
             }
             
    }else if($uri == 'produtos') {
        http_response_code(200);
        $dados = Crud::select(
            'SELECT * FROM estoque',
            [],
            TRUE
        );
        echo json_encode($dados);
    }else {
        http_response_code(406);
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
  
    if(!is_numeric($uri)){
        http_response_code(406);
    }else {
            $dados = Crud::select('SELECT * FROM estoque WHERE ID = :ID',['ID' => $uri],FALSE);
                       
            if (!empty($dados)) {
                Crud::setTabela('estoque');
                $retorno = Crud::delete(['ID' => $uri]);
                if ($retorno) {
                    
                    Crud::setTabela('movimentacao_estoque');
                    $retorno = Crud::delete(['id_produto' => $uri]);

                    http_response_code(202);
                    echo json_encode(['mensagem' => 'Excluído com sucesso!']);
                }else {
                    http_response_code(500);
                    echo json_encode(['mensagem' => 'Erro interno no servidor']);
                }
            }else {
                http_response_code(404);
                echo json_encode(['mensagem' => 'Id não encontrado']);
            }
        }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $descricao = (isset($_POST['descricao'])) ? $_POST['descricao'] : '';
   
    

    if (empty($descricao)) {
        echo json_encode(['mensagem' => 'informe uma descrição']);

        http_response_code(406);
    }
if($uri == 'produtos'):

    Crud::setTabela('estoque');
    $retorno = Crud::insert(['descricao' => $descricao, 'saldo_atual' => 0]);



    if ($retorno) {
        http_response_code(201);
        echo json_encode(['mensagem' => 'Inserido com sucesso']);
    } else {
        http_response_code(500);

        echo json_encode(['mensagem' => 'Erro no servidor']);
    }
else:
    http_response_code(404);

    echo json_encode(['mensagem' => 'Not Found']);

endif;
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
   
    parse_str(file_get_contents('php://input'), $_PUT);

    $id = (isset($_PUT['id']) ? $_PUT['id'] : ''); 
    $tipoMovimentacao = (isset($_PUT['tipo']) ? $_PUT['tipo'] : '');
    $qtde = (isset($_PUT['qtde']) ? $_PUT['qtde'] : '');
    
    
    if ($qtde === '0') {
        echo json_encode(['Mensagem' => 'Valor Inválido']);
        http_response_code(406);
        exit;
    }
    if ($id === '0' || $id == '') {
        echo json_encode(['Mensagem' => 'Id Inválido']);
        http_response_code(406);
        exit;
    }
    if (!is_numeric($id)) {
        http_response_code(406);
        echo json_encode(['mensagem' => 'O id deve ser um número']);
        exit;
     }
     if($uri == 'produtos'){

        $dados = Crud::select(
            'SELECT * FROM estoque WHERE ID = :ID',
            ['ID' => $id],
            TRUE
        );

        $getSaldoAntes = (array) $dados;

        if ($tipoMovimentacao === 'ENTRADA') {
            $saldoDepois = $getSaldoAntes[0]->saldo_atual + $qtde;

            Crud::setTabela('movimentacao_estoque');
                $inserirMovimentacao = Crud::insert(['tipo' => $tipoMovimentacao, 'id_produto' => $id, 'saldo_antes' => $getSaldoAntes[0]->saldo_atual, 'saldo_depois' => $saldoDepois, 'qtde' => $qtde]);
        }elseif ($tipoMovimentacao === 'SAIDA') {
            
            $saldoDepois = $getSaldoAntes[0]->saldo_atual - $qtde;
            if ($saldoDepois < 0) {

                $saldoDepois = 0;
            }

            Crud::setTabela('movimentacao_estoque');
            $inserirMovimentacao = Crud::insert(['tipo' => $tipoMovimentacao, 'id_produto' => $id, 'saldo_antes' => $getSaldoAntes[0]->saldo_atual, 'saldo_depois' => $saldoDepois, 'qtde' => $qtde]);
        }

        

         Crud::setTabela('estoque');
         $retorno = Crud::update(['descricao' => $dados[0]->descricao, 'saldo_atual' => $saldoDepois],['ID' => $id]);

        if ($retorno) {
        http_response_code(202);
        echo json_encode(['mensagem' => 'Atualizado com sucesso!']);
    }else {
        http_response_code(500);
        echo json_encode(['mensagem' => 'Erro interno no servidor']);
    }
     }

}




/*
* GET -> CONSULTA DE DADOS
* POST -> INCLUSÃO (CREATE)
* PUT -> ATUALIZAÇÃO
* DELETE -> EXCLUSÃO
*/

/*
* 200 -> get consulta realizada com sucesso
* 404 -> not found (não encontrado)
* 201 -> inserido com sucesso (created)
* 202 -> atualizado ou excluido com sucesso
* 406 -> parametros incorretos
* 401 -> não autorizado
*/
//  $teste = (array) $dados;
// echo var_dump($teste[0]->saldo_atual);