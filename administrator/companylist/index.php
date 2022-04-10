<?php
    session_start();
    require_once('../../functions/common/function.php');
    require_once('../../functions/common/connectdb.php');
    //ログインできていない場合はログイン画面に戻る
    if(!security::isAdmin($_SESSION["auth"])){
        header("Location: ../");
    }



    $pdo=connectDb();

    $stmt = $pdo->query('SELECT company_code AS COMPANY_CODE ,company_name AS COMPANY_NAME,address AS ADDRESS,homepage AS HOMEPAGE FROM COMPANIES ORDER BY COMPANY_CODE');

    $result = $stmt->fetchAll();
    
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="../../css/reset.css">
    <link rel="stylesheet" href="../../css/admin/style.css">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <meta charset="UTF-8">
    <title>ADMIN</title>
</head>
<body>
    <header><!--ヘッダー-->
        <div class="header-contents">
            <div class="header-logo"><a href="">DenB Admin</a><!--ロゴ--></div>
            <div class="header-search">
                <form class ="frm" name=form1 method = "get" action = "search/">
                    <input type = "text" name = "name" class = "search-company" id="search-company" placeholder="企業名から探す...">
                    <button class="btn" type="admit"><i class="fas fa-search mysearch"></i></button>
                </form>
            </div>
            <div class="header-list">
                <ul class="header-ul">
                    <?php if(security::isStudent($_SESSION["auth"])):  ?><li class = "topleftbar" id = "uploadbutton">投稿</li><?php endif; ?>
                    <?php if(security::isAdmin($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href="./">管理者</a></li><?php } ?>
                    <?php if(security::isTeacher($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href = "../../teacher/">教師専用</a></li><?php } ?>
                    <li class = "topleftbar"><a class="btnrightbar" href = "account/">アカウント</a></li>
                </ul>
            </div>
        </div>
    </header><!--ヘッダー-->
    <main>
        <div class="contents">
            <div class="leftbar">
                <ul>
                    <li class="admin-home" id="selected">ホーム</li>
                    <li class="teacher-list-button">教師リスト</li>
                    <li class="student-list-button">生徒リスト</li>
                    <li class="company-list-button">企業リスト</li>
                    <li class="report-list-button">報告書リスト</li>
                </ul>
            </div>
            <div class="box">
                <!-- select box-->
                <table border="3" bordercolor="black">
                    <tr class="top-label">
                        <th>企業コード</th>
                        <th>企業名</th>
                        <th>住所</th>
                        <th>ホームページ</th>
                    </tr>
                    <?php foreach($result as $result): ?>
                    <tr>
                        <th><?php echo $result['COMPANY_CODE']?></th>
                        <th><?php echo $result['COMPANY_NAME']?></th>
                        <th><?php echo $result['ADDRESS']?></th>
                        <th><?php echo $result['HOMEPAGE']?></th>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>
    <script>
        $('.admin-home').click(function(){
            window.location.href = '../';
        });
        $('.teacher-list-button').click(function(){
            window.location.href = '../teacherlist/';
        });
        $('.company-list-button').click(function(){
            window.location.href = '../companylist/';
        });
        $('.report-list-button').click(function(){
            window.location.href = '../reportlist/';
        });
    </script>
</body>
</html>