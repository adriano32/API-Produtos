<?php

require_once 'Class.conexao.php';

class Crud
{

    private static $conexao;
    private static $tabela;

    public static function setConexao($conn)
    {
        self::$conexao = $conn;
    }

    public static function setTabela($nomeTabela)
    {
        self::$tabela = $nomeTabela;
    }

    public static function montaSQLInsert($arrayDados)
    {

        $campos = implode(',', array_keys($arrayDados));
        $params = ':' . implode(', :', array_keys($arrayDados));

        $sql = 'INSERT INTO ' . self::$tabela;
        $sql .= '(' . $campos . ')VALUES(' . $params . ')';
        return $sql;
    }

    public static function montaSQLDelete($arrayCondicoes)
    {

        $sql = 'DELETE FROM ' . self::$tabela;
        $sql .= ' WHERE ';

        foreach ($arrayCondicoes as $key => $value) {
            $sql .= " {$key} = :{$key} AND";
        }
        $sql = rtrim($sql, 'AND');
        return $sql;
    }

    public static function montaSQLUpdate($arrayDados, $arrayCondicoes)
    {

        $sql = 'UPDATE ' . self::$tabela . ' SET ';

        foreach ($arrayDados as $key => $value) {
            $sql .= "{$key} = :{$key}, ";
        }
        $sql = rtrim($sql, ', ');
        $sql .= ' WHERE ';

        foreach ($arrayCondicoes as $key => $value) {
            $sql .= "{$key} = :{$key} AND";
        }
        $sql = rtrim($sql, 'AND');

        return $sql;
    }

    public static function insert($arrayDados)
    {
        try {
            $sql = self::montaSQLInsert($arrayDados);
            $stm = self::$conexao->prepare($sql);

            foreach ($arrayDados as $key => $value) {
                $stm->bindValue(':' . $key, $value);
            }

            $retorno = $stm->execute();
            return $retorno;
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }


    public static function delete($arrayCondicoes)
    {

        try {
            $sql = self::montaSQLDelete($arrayCondicoes);
            $stm = self::$conexao->prepare($sql);

            foreach ($arrayCondicoes as $key => $value) {
                $stm->bindValue(':' . $key, $value);
            }

            $retorno = $stm->execute();
            return $retorno;
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function update($arrayDados, $arrayCondicoes)
    {



        try {
            $sql = self::montaSQLUpdate($arrayDados, $arrayCondicoes);
            $stm = self::$conexao->prepare($sql);

            foreach ($arrayDados as $key => $value) {
                $stm->bindValue(':' . $key, $value);
            }

            foreach ($arrayCondicoes as $key => $value) {
                $stm->bindValue(':' . $key, $value);
            }

            $retorno = $stm->execute();
            return $retorno;
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function select($sql, $arrayCondicoes, $fetchAll){

        try {
            $stm = self::$conexao->prepare($sql);

            foreach ($arrayCondicoes as $key => $value){

                $stm->bindValue(':'.$key, $value);

            }

            $stm->execute();

            if ($fetchAll) {

               return $stm->fetchAll(PDO::FETCH_OBJ);

            } else{

                return $stm->fetch(PDO::FETCH_OBJ);

            }
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }

    }
}


$pdo = Conexao::getConexao();
Crud::setConexao($pdo);
Crud::setTabela('estoque');

//INSERT

//$dados = Crud::insert(['NOME' => 'Adriano','EMAIL' => 'adriano@gmail.com', 'TIPO' => 'FISICO', 'ID_ROTA' => 89]);

//UPDATE

// $retorno = Crud::update(['EMAIL' => 'adriano@gmail.com'], ['ID' => 1017]);

// if ($retorno) {
//     echo 'ATUALIZADO';
// } else {
//     echo 'ERRO';
// }


//SELECT






// $dados = Crud::select(
//     'SELECT * FROM tb_pessoa WHERE ID = :ID',
//     ['ID' => 1017],
//     TRUE
// );
// echo '<pre>';
// var_dump($dados);

//DELETE

// $dados = Crud::delete([
//     'ID' => 1017,
    
// ]
// );

//SELECT

// $dados = Crud::select(
//     'SELECT * FROM tb_pessoa WHERE ID_ROTA = :ID_ROTA',
//     ['ID_ROTA' => 1],
//     TRUE
// );
// echo '<pre>';
// var_dump($dados);

//tb_produto

//INSERT

//$dados = Crud::insert(['DESCRICAO' => 'PRODUTO','VALOR' => 1.00, 'STATUS' => 'ATIVO']);



//UPDATE

// $retorno = Crud::update(['VALOR' => 5.00], ['ID' => 1104]);

// if ($retorno) {
//     echo 'ATUALIZADO';
// } else {
//     echo 'ERRO';
// }

//SELECT

// $dados = Crud::select(
//         'SELECT * FROM tb_pRODUTO WHERE ID = :ID',
//         ['ID' => 1104],
//         TRUE
//     );
//     echo '<pre>';
//     var_dump($dados);


//DELETE

// $dados = Crud::delete(
//     ['ID' => 1104]
// );

//SELECT

// $dados = Crud::select(
//     'SELECT * FROM tb_produto where ID = :ID',
//     ['ID' => 1103],
//     true
// );

// echo '<pre>';
//    var_dump($dados);

//--------------------------------------------------------------------




// $dados = Crud::select(
//     'SELECT * FROM tb_pessoa WHERE ID > :ID',
//     ['ID' => 1000],
//     TRUE
// );
// echo '<pre>';
// var_dump($dados);

//$retorno = Crud::update(['ID_ROTA' => 1], ['ID' => 1016]);

// if ($retorno) {
//     echo 'ATUALIZADO';
// } else {
//     echo 'ERRO';
// }




//echo Crud::montaSQLInsert(['nome' => 'william', 'cpf' => '1641641651']);
//echo Crud::montaSQLDelete(['id' => '12', 'tipo' => 'juridico']);
//echo Crud::montaSQLUpdate(['tipo' => 'juridico'],['id' => 345]);