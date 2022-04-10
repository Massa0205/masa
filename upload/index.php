<?php
   /* session_start();
    if(isset($_SESSION['login'])==false){//ログインしていない
        header("Location: ../login/");
        exit();
    }
    $pdo = new PDO('mysql:host=localhost;dbname=masa', 'root', 'root');
    $sql = 'SELECT COUNT(*) AS CNT FROM REPORT_TYPE';
    $pdostmt = $pdo->query($sql);
    $rowcnt = $pdostmt->fetchAll();

    $spl = 'select id,name from report_type';
    $pdostmt = $pdo->query($sql);
    $data = $pdostmt->fetchAll();

    if(isset($_POST['company'])){
        $pdostatement=$pdo->prepare('select company_code from companies where company_name = ?');
        $pdostatement->execute(array($_POST['company']));
        $row = $pdostatement->fetchAll();
        if(isset($row)){
            $com_name = $row[0]['company_code'];
            $pdostatement1=$pdo->prepare('insert into report_waiting_admit (poster,company_code,type,impression,report_date,admit_status) values (?,?,?,?,?,0)');
            $pdostatement1->execute(array($_SESSION["login"],$com_name,$_POST['reporttype'],$_POST['naiyou'],$_POST['repdate']));
            header("Location: ../");
        }
        ?>
        <?php
    }*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>

    
