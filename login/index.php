<?php


session_start();
/* すでにログイン済み */
if (isset($_SESSION["login"])) {
    session_regenerate_id(TRUE);
    header("Location: ../");
}

required_once("../functions/common/connectdb.php");

$pdo=connectDb();
$str = "";


/*
    フォームから送信されている
*/
if(isset($_POST['user_id'])):
    $id   = $_POST['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
    $stmt->execute(array($id));
    $row  = $stmt->fetch();

    /* パスワードチェック */
    /*　　失敗 */
    if(!password_verify($_POST['pass'],$row['password'])){
        echo 'あいうえお'.$row['password'].$_POST['pass'];
        $str = 'ログインに失敗しました';
    }
    /* 成功 */
    else{
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
endif;
?>

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