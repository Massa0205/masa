<?php 
    session_start();
    if(isset($_SESSION["login"])==false){//セッションIDが発行されていない場合はloginページに戻る
        header("Location: ../../login/");
    }
    require_once("../../functions/common/connectdb.php");
    require_once("../../functions/common/function.php");
    $pdo = connectDb();
    
    if(!isset($_GET["name"])){//検索バー未入力
        $issetCompany = false;
        $stmt = $pdo->prepare('SELECT SUB.CN AS COMNAME,SUB.CNT AS REPCNT,SUB.DP AS MOSTDEP,MAX(SUB.CNT) AS MOSTDEPCNT
                                    FROM (SELECT R.company_code AS CC,C.company_name AS CN,COUNT(R.company_code) AS CNT,S.department AS DP
                                            FROM reports R
                                            JOIN students S
                                            ON R.poster = S.student_number
                                            JOIN companies C
                                            ON R.company_code = C.company_code
                                            GROUP BY R.company_code,S.department) AS SUB
                                            GROUP BY SUB.CC
                                            ORDER BY COMNAME');
        $stmt->execute();
    }
    else{
        $issetCompany = true;
        $stmt = $pdo->prepare('SELECT SUB.CN AS COMNAME,SUB.CNT AS REPCNT,SUB.DP AS MOSTDEP,MAX(SUB.CNT) AS MOSTDEPCNT
                                FROM (SELECT R.company_code AS CC,C.company_name AS CN,COUNT(R.company_code) AS CNT,S.department AS DP
                                    FROM reports R
                                    JOIN students S
                                    ON R.poster = S.student_number
                                    JOIN companies C
                                    ON R.company_code = C.company_code
                                    AND C.company_name like ?
                                    GROUP BY R.company_code,S.department) AS SUB
                                    GROUP BY SUB.CC
                                    ORDER BY COMNAME');
        $stmt->execute(array('%'.$_GET["name"].'%'));
    }
    $count = $stmt->rowCount();
    $companyData = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet"  href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" >
    <link rel="stylesheet" href="../../css/common/reset.css">
    <link rel="stylesheet" href="../../css/common/autocomp.css">
    <link rel="stylesheet" href="../../css/companies/style.css">
    <link rel="stylesheet"  href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <title>企業リスト</title>
</head>
<body>
    <header><!--ヘッダー-->
        <div class="header-contents">
            <div class="header-logo"><a href="../../">DenB</a><!--ロゴ--></div>
            <div class="header-search">
                <form class ="frm" name=form1 method = "get" action = "../">
                    <input type = "text" name = "name" class = "search-company" id="search-company" placeholder="企業名から探す..." value="<?php if($issetCompany){echo $_GET["name"];}?>">
                    <button class="btn" type="admit"><i class="fas fa-search mysearch"></i></button>
                </form>
            </div>
            <div class="header-list">
                <ul class="header-ul">
                    <?php if(security::isStudent($_SESSION["auth"])){  ?><li class = "topleftbar" id = "uploadbutton">投稿</li><?php } ?>
                    <?php if(security::isAdmin($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href="#">管理者</a></li><?php } ?>
                    <?php if(security::isTeacher($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href = "teacher/">教師専用</a></li><?php } ?>
                    <li class = "topleftbar"><a class="btnrightbar" href = "../../account/">アカウント</a></li>
                </ul>
            </div>
        </div>
    </header><!--ヘッダー-->
        <div class="upload-modal-wapper">
            <form class="frmupload" method = "post" action = "/add/">
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
            <?php 
            /********************************要修正**********************************/
            if(!!isset($_GET["name"]) && $count == 0): ?>
                <div class="message"><?php echo $_GET["name"]?>は検索にマッチしませんでした。</div>
            <?php endif; ?>
            <?php foreach($companyData as $rows): ?>
                <div class="company-box">
                    <a class="company-name" href="../?name=<?php echo $rows["COMNAME"]?>"><?php echo $rows["COMNAME"] ?></a>
                    <p class="report-counter"><?php echo '投稿'.$rows["REPCNT"].'件'?></p>
                    <p class="txt-company-info"><?php echo '主に'.$rows["MOSTDEP"].'の生徒が受けています' ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <script src="../../script/common/autocomp_ajax.js"></script>
    <script src="../../script/common/modal.js"></script>
</body>
</html>