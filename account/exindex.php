<?php
    session_start();

    if(!isset($_SESSION["login"])){
        header("Location: ../");
    }
    require_once("../functions/common/function.php");
    require_once("../functions/common/connectdb.php");


    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_number = ?");
    $stmt->execute(array($_SESSION["login"]));
    $data = $stmt->fetch();

    $stmt2 = $pdo->prepare("SELECT RE.company_code AS COMP_ID,RE.poster AS POSTER,RE.report_date AS POSTDATE,RE.impression AS IMPRESSION,CO.company_name AS COMPANYNAME,ST.name AS STUDENTNAME,ST.department AS DEPARTMENT,TY.name AS REPTYPE 
                            FROM reports RE JOIN companies CO ON RE.company_code = CO.company_code 
                            JOIN students ST ON RE.poster = ST.student_number
                            AND ST.student_number = ?
                            JOIN report_type TY ON RE.type = TY.id 
                            ORDER BY RE.report_date DESC");
    $stmt2->execute(array($_SESSION["login"]));
    $reports = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "utf-8">
    <title>トップページ</title>
    <link rel="stylesheet" href="../css/common/reset.css">
    <link rel="stylesheet" href="../css/account/style.css">
    <link rel="stylesheet" href="../css/common/autocomp.css">
    <link rel="stylesheet"  href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" >
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!--<link rel="stylesheet" href = "registerstyle.css">-->
</head>
<body>
    <header><!--ヘッダー-->
        <div class="header-contents">
            <div class="header-logo"><a href="../">DenB</a><!--ロゴ--></div>
            <div class="header-search">
                <form class ="frm" name=form1 method = "get" action = "../search/">
                    <input type = "text" name = "name" class = "search-company" id="search-company" placeholder="企業名から探す...">
                    <button class="btn" type="admit"><i class="fas fa-search mysearch"></i></button>
                </form>
            </div>
            <div class="header-list">
                <ul class="header-ul">
                    <?php if(security::isStudent($_SESSION["auth"])){  ?><li class = "topleftbar" id = "uploadbutton">投稿</li><?php } ?>
                    <?php if(security::isAdmin($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href="#">管理者</a></li><?php } ?>
                    <?php if(security::isTeacher($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href = "teacher/">教師専用</a></li><?php } ?>
                    <li class = "topleftbar"><a class="btnrightbar" href = "">アカウント</a></li>
                </ul>
            </div>
        </div>
    </header><!--ヘッダー-->
    <div class="upload-modal-wapper">
            <form class="frmupload" method = "post" action = "/upload/">
                <i class="fas fa-times size" id="closebtn"></i>
                <div class="frmmain">
                    <ul>
                        <li class="marginmargin">　</li>
                        <div class = "company-tab">
                        <li>企業名</li>
                            <li><input name ="company" type="text" class="search-company" placeholder = "（例）熊本電子ビジネス専門学校" required></li>
                        </div>
                        <div class = "reporttype">
                            <li>種類</li>
                            <li>
                                <select size="1" name="reporttype" required>
                                    <option value="">--選択してください--</option>
                                    <option value="1">説明会　 (オンライン)</option>
                                    <option value="2">説明会　 (対面)</option>
                                    <option value="3">面談  　 (オンライン))</option>
                                    <option value="4">面談  　 (対面)</option>
                                    <option value="5">面接  　 (オンライン)</option>
                                    <option value="6">面接  　 (対面)</option>
                                    <option value="7">筆記試験 (オンライン)</option>
                                    <option value="8">筆記試験 (対面)</option>
                                    <option value="9">実技試験 (オンライン)</option>
                                    <option value="10">実技試験 (対面)</option>
                                    <option value="11">その他</option>     
                                </select>      
                            </li>
                        </div>
                        <div class ="examdate">
                            <li>実施日</li>
                            <li><input name="repdate" type="date" required></li>
                        </div>
                        <div class="impress">
                            <li>感想</li>
                            <li><textarea name = "naiyou" rows="16" placeholder = "ここに感想、内容を記入" required></textarea></li>
                        </div>
                        <li><input id = "sendbtn" type = "submit" value = "投稿"></li>
                    </ul>    
                </div>      
            </form>
        </div>
    <main>
        <div class="contents">
            <ul class="info">
                <li>  学籍番号    <?php echo $_SESSION["login"]?></li>
                <li>    氏名      <?php echo $reports[0]["STUDENTNAME"] ?></li>
            </ul>
            <button id="logout-button">ログアウト</button>
            <div class="btn-changepass" id="notclicked">パスワード変更</div>
            <ul id="set-pass">
                <li>変更後のパスワードを入力してください</li>
                <li><input class="pass-box"type="password" placeholder="パスワードを入力"><input id="btn-submit" type="button" value="変更"></li>
            </ul>
            <h2>報告履歴</h2>
            <div class="report-box">
                <?php foreach($reports as $reports): ?>
                <div class="reports">
                    <ul class="reports-info">
                        <li class="report-company"><?php echo $reports["COMPANYNAME"]?></li>
                        <li class="report-type"><?php echo $reports["REPTYPE"] ?></li>
                        <?php $work_date=new Datetime($reports['POSTDATE']);?>
                        <li class="report-date"><?php echo date_format($work_date,'Y年m月d日'); ?></li>
                    </ul>
                    <p class="naiyo" ><?php echo $reports["IMPRESSION"]?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <script src="../script/common/modal.js"></script>
    <script src="../script/common/autocomp_ajax.js"></script>
    <script src="../script/account/script.js"></script>
</body>
</html>