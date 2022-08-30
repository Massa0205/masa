<?php
    session_start();

    require_once("../functions/common/function.php");
    if(!security::isAdmin($_SESSION["auth"])){
        header("Location: ../");
    }

    $output = null;
    $retval = null;
    exec("ps -eo lstart,pid,args | grep httpd",$output,$retval);

    $spaceCounter = 0;
    $point = 0;
    for($i = 0;$i < strlen($output[19]);$i+=1){
        if($output[19][$i] == " "){
            $spaceCounter += 1;
        }
        if($spaceCounter == 4){
            $point = $i;
            break;
        }
    }
    $str = substr($output[19],0,$point);
    

    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Top</title>
    <link rel="stylesheet"  href="../css/common/reset.css">
    <link rel="stylesheet"  href="../css/common/header.css">
    <link rel="stylesheet"  href="../css/common/autocomp.css">
    <link rel="stylesheet"  href="../css/common/adminheader.css">
    <link rel="stylesheet"  href="../css/admin/top/style2.css">
    <link rel="stylesheet"  href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" >
    <link rel="stylesheet"  href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
    <?php include '../functions/common/header.php' ?>
    <div class="left-bar">
        <ul class="left-bar-list">
            <li class="left-bar-selected"    id="left-bar-btn-top">トップ</li>
            <li class="left-bar-notselected" id="left-bar-btn-teacherlist">教師権限管理</li>
            <li class="left-bar-notselected" id="left-bar-btn-studentlist">生徒管理</li>
            <li class="left-bar-notselected" id="left-bar-btn-reportslist">報告書管理</li>
        </ul>
    </div>
    <div class="contents" >
        <p>サーバー起動日時 <span class="last-date"><?php echo $str ?></span></p>
        <p>最後のバックアップ <span class="last-date">2022年2月10日</span></p>
        <button>今すぐバックアップを取る</button>
    </div>
    <script src="../script/common/autocomp_ajax.js"></script>
</body>
</html>