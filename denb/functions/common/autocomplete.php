<?php 

    try{
        $pdo = new PDO('mysql:host=localhost;dbname=masa', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
    $pdostmt = $pdo->prepare("select company_name from companies where company_name like ?");
    $pdostmt->execute(array('%'.$_POST['param1'].'%'));
    for($i = 0;$i<20 && $comname = $pdostmt->fetch(PDO::FETCH_ASSOC);$i++){
        $array[$i]=$comname["company_name"];
    }
    echo json_encode($array);
?>