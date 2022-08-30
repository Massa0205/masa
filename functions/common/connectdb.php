<?php
    /**
     * DB接続してPDOを返すメソッド
     * 失敗時はNULLを返す
     * @return PDO 
    */
    class DataBaseConnector{

        private $pdo = null;
        private const HOST = 'localhost';
        private const NAME = 'masa';
        private const ID   = 'root';
        private const PASS = 'root';

        function EstablishConnection(){
            /**
             * コネクション確立
             */
            $this->pdo = new PDO('mysql:host=${HOST}; dbname=${NAME}', '${ID}', '${PASS}');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
    }
?>