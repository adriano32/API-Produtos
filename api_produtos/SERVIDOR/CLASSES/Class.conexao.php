<?php

define('HOST','localhost');

define('DBNAME', 'prova');

define('USER', 'root');

define('PASSWORD','');


class conexao {


    private static $pdo;


    private function __construct(){}

    public static function getConexao(){
        if(!isset(self::$pdo)){

            $opcoes = [ PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];

            self::$pdo = new PDO('mysql:host='.HOST.';dbname='.DBNAME.';',USER,PASSWORD, $opcoes);
  

        
        }

        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$pdo;

    }

   
}

//$conexao = Conexao::getConexao();
//var_dump($conexao);