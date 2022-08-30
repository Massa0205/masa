<?php
    /**
     * DB接続してPDOを返すメソッド
     * 失敗時はNULLを返す
     * @param なし
     * @return PDO 
     * 
     * 
    */
    function connectDb(){
        
        try{

            $pdo = new PDO('mysql:host=localhost;dbname=masa', 'root', 'root');
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            
        }
        catch(Exception $e){

            $pdo = null;

        }
        finally{

            return $pdo;

        }
    }
?>