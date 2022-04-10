<?php


session_start();
 /*required_once("../functions/common/connectdb.php");
 $pdo=connectDb();*/
$str = "";
try{
    $pdo = new PDO('mysql:host=localhost;dbname=masa', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

}
catch(PDOException $e){
    echo $e->getMessage();
}
//セッションIDがあったら
if (isset($_SESSION["login"])) {
    session_regenerate_id(TRUE);
    header("Location: ../");
    exit();
}

//フォームから送信されていたら
if(isset($_POST['user_id'])){
    $id = $_POST['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
    $stmt->execute(array($id));
    $row = $stmt->fetch();
    //一件だけ

    if(!password_verify($_POST['pass'],$row['password'])){//失敗
        echo 'あいうえお'.$row['password'].$_POST['pass'];
        $str = 'ログインに失敗しました';
    }
    else{//成功
        session_regenerate_id(TRUE);
        $_SESSION["login"]= $id;
        $_SESSION["auth"] = $row["authority"];
        $pdostatement2 = $pdo->prepare('SELECT * FROM students WHERE student_number = ?');
        $pdostatement2->execute(array($id));
        $result = $pdostatement2->fetchAll();
        $_SESSION["UserDepartment"]=$result[0]['department'];
        header("Location: ../");
        exit();
    }
}
?>

<!---------------HTML-------------------->

<!DOCTYPE html>
<html lang="ja">
<head>
    <link href = "../css/login/style.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>ログイン</title>
</head>
<body>
    <div class = "content">
        <form class="frm" action = "" method = "POST"><!--箱-->
            <h1 class = "logo">DenB</h1>
            <div class = "inputlist">
                <input class = "inputmail" type ="text" name = "user_id" placeholder = "学籍番号" autofocus autocomplete="on" required>
                <input class = "inputpass" type ="password" name = "pass" placeholder = "パスワード" required>
                <input class = "submitbtn"type ="submit" value="ログイン">
            </div>
        </form><!--終了-->
    </div>
</body>
</html>         