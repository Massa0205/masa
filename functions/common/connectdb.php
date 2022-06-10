<?php
    /*
        @return PDOstatement
        DB接続失敗時はNULL

    */
    function connectDb(){
        try{
            $pdo = new PDO('mysql:host=localhost;dbname=masa', 'root', 'root');
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }
        catch(Exception $e){
            return $db = null;
        }
    }
?>