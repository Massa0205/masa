<?php
    if(isset($_SESSION["login"])==false){//セッションIDが発行されていない場合はloginページに戻る
        header("Location: ../../");
    }

    /******DB接続処理******/
    try{
        $pdo = new PDO('mysql:host=localhost;dbname=masa', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
    $num = $_POST['number'];
    if($_POST["type"]=="remove"){
        $statement=$pdo->prepare('delete from report_waiting_admit where waiting_number = ?');
        $statement->execute(array($_POST['number']));
        exit;
    }

    if($_POST["currentStatus"] < 4){
        $status = $_POST["currentStatus"];
        $status = $status + 1;
        $statement=$pdo->query('START TRANSACTION');
        $statement=$pdo->prepare('update report_waiting_admit set admit_status = ? where waiting_number = ?');
        $statement->execute(array($status,$_POST['number']));
        $statement=$pdo->query('COMMIT');
    }
    else{//校長が承認したらレポートテーブルへ
        $stmt = $pdo->prepare('select * from report_waiting_admit where waiting_number = ?');
        $stmt->execute(array($_POST['number']));
        $result = $stmt->fetch();
        $stmt2 = $pdo->prepare('insert into reports(poster,company_code,type,impression,report_date)');
        $stmt2->execute(array($result["poster"],$result["company_code"],$result["type"],$result["impression"],$result["report_date"]));
        $stmt3 = $pdo->prepare('delete from report_waiting_admit where waiting_number = ?');
        $stmt->execute(array($_POST['number']));
    }
    
?>