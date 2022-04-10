<?php
    session_start();
    require_once('../../functions/common/function.php');
    require_once('../../functions/common/connectdb.php');
    //ログインできていない場合はログイン画面に戻る
    if(!security::isAdmin($_SESSION["auth"])){
        header("Location: ../");
    }



    $pdo=connectDb();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Top</title>
    <link rel="stylesheet"  href="../../css/common/reset.css">
    <link rel="stylesheet"  href="../../css/common/header.css">
    <link rel="stylesheet"  href="../../css/common/autocomp.css">
    <link rel="stylesheet"  href="../../css/common/adminheader.css">
    <link rel="stylesheet"  href="../../css/admin/top/style.css">
</head>
<body>
    <?php include '../../functions/common/header.php' ?>
    <div class="left-bar">
        <ul class="left-bar-list">
            <li class="left-bar-notselected" id="left-bar-btn-top">トップ</li>
            <li class="left-bar-notselected" id="left-bar-btn-teacherlist">教師権限管理</li>
            <li class="left-bar-selected"    id="left-bar-btn-studentlist">生徒管理</li>
            <li class="left-bar-notselected" id="left-bar-btn-reportslist">報告書管理</li>
            <li class="left-bar-notselected" id="left-bar-btn-companylist">企業管理</li>
        </ul>
    </div>
    <div class="contents" >
        
    </div>

</body>
</html>