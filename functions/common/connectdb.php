<?php
    function connectDb(){
        try{
            $pdo = new PDO('mysql:host=localhost;dbname=masa', 'root', 'root');
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }
        catch(PDOException $e){
            return $db=null;
        }
    }
?>