<?php
    if(!isset($_POST['param'])){
        header("Location:../../");
    }
    require_once("../common/connectdb.php");
    $pdo = connectDb();
    if(!isset($pdo)){
        header("Location:../../error/");
    }
    
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE company_name LIKE ?");
    $stmt->execute(array('%'.$_POST['param'].'%'));

    $result = $stmt->fetchAll();
    foreach($result as $result){
        $array[]=array(
            'COMPANYNAME' => $result['company_name']
        );
    }
    echo json_encode($array,JSON_UNESCAPED_UNICODE);