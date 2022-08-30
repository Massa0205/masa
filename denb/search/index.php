<?php

    session_start();
    //ログイン管理
    if(isset($_SESSION["login"])==false){
        header("Location: ../login/");
        exit();
    }
    //pdo受け取り
    require_once("../functions/common/function.php");
    require_once("../functions/common/connectdb.php");
    $pdo = connectDb();

    /*
    *    DB接続チェック
    */
    if(!isset($pdo)){
        header("Location: /error/");
    }
    /*
    *    企業名が送信されていなかったら
    */
    if(!isset($_GET['name'])){
        header("Location: /");
    }
    /*
    *   特殊文字が含まれる or 送信された企業名が空白
    */
    if (preg_match('/^\s*$/u', $_GET['name']) || $_GET['name'] == "" ) {
        header("Location: /search/companies/");
    }
    /***おぼえてない */
    $sortType = -1;
    $type[0] = 1;
    $type[1] = 13;    
    /****入力された企業名から企業情報を取得する問い合わせ****/
    $stmt = $pdo->prepare("select * from companies where company_name = ?");
    $stmt->execute(array($_GET['name']));
    $count = $stmt->rowCount();
    $result = $stmt->fetchAll();
    /***入力した企業名の
     * 
     * $stmt3 = $pdo->prepare()データが存在しない***/
    if($count == 0 || $count>1){
        //企業が存在しないページ
        header("Location: ./companies/?name=".$_GET["name"]);
    }    
    $str = $result[0]['company_code'];
    $name = $_GET['name'];
    
    /*****企業コード検索終了**** */
    $stmt2 = $pdo->prepare("SELECT A.poster AS POSTER,A.report_date AS POSTDATE,A.impression AS IMPRESSION,B.company_name AS COMPANYNAME,C.name AS STUDENTNAME,C.department AS DEPARTMENT,D.name AS REPTYPE 
            FROM reports A JOIN companies B ON A.company_code = B.company_code AND A.company_code = ?
            JOIN students C ON A.poster = C.student_number 
            JOIN report_type D ON A.type = D.id AND (D.id <= ? AND D.id >= ?)
            ORDER BY A.report_date DESC;");
    $stmt2->execute(array($str,$type[1],$type[0]));

    $stmt3 = $pdo->prepare("SELECT COUNT(RE.impression) AS cnt,TY.name FROM reports RE
                            RIGHT OUTER JOIN report_type TY
                            ON RE.type = TY.id
                            AND RE.company_code = ?
                            GROUP BY RE.type,TY.name
                            ORDER BY TY.id");
    $stmt3->execute(array($str));
    $type_count_result = $stmt3->fetchAll();

?>

<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "utf-8">
    <title>検索結果</title> 
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link href="../css/common/reset.css" rel="stylesheet" type="text/css">
    <link href="../css/common/autocomp.css" rel="stylesheet" type="text/css">
    <link href="../css/search/style.css" rel="stylesheet" type="text/css">
</head>
    <body>
        <header><!--ヘッダー-->
            <div class="header-contents">
                <div class="header-logo"><a href="../">DenB</a><!--ロゴ--></div>
                <div class="header-search">
                    <form class ="frm" name=form1 method = "get" action = "">
                        <input type = "text" name = "name" class="search-company" placeholder="企業名から探す..." value="<?php echo $_GET['name'] ?>"> 
                        <button class="btn" type="admit"><i class="fas fa-search mysearch"></i></button>
                    </form>
                </div>
                <div class="header-list">
                    <ul class="header-ul">
                        <?php if(security::isStudent($_SESSION["auth"])){  ?><li class = "topleftbar" id = "uploadbutton">投稿</li><?php } ?>
                        <?php if(security::isAdmin($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href="../administrator/">管理者</a></li><?php } ?>
                        <?php if(security::isTeacher($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href = "../teacher/">教師専用</a></li><?php } ?>
                        <li class = "topleftbar"><a class="btnrightbar" href = "../account/">アカウント</a></li>
                    </ul>
                </div>
            </div>
        </header><!--ヘッダー-->
        <!----------------------------------------------------メイン------------------------------------------------------>
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
                <h1 class = "company-namelabel"><?php echo $name ?><span>についてのレポート</span></h1>
                <!--<img id="company-img" src="../images/logo2.png">-->
                    <ul class="type-select">
                        <li id = "alltype">すべて(<?php $sum = 0 ;for($i=0;$i<count($type_count_result);$i+=1){ $sum += $type_count_result[$i]["cnt"]; } echo $sum?>)</li>
                        <li class="marginbar" id = "説明会" value="説明会">説明会(<?php echo $type_count_result[0]["cnt"]+$type_count_result[1]["cnt"] ?>)</li>
                        <li class="marginbar" id = "面談" value="面談">面談(<?php echo $type_count_result[2]["cnt"]+$type_count_result[3]["cnt"] ?>)</li>
                        <li class="marginbar" id = "面接" value="面接">面接(<?php echo $type_count_result[4]["cnt"]+$type_count_result[5]["cnt"] ?>)</li>
                        <li class="marginbar" id = "試験" value="試験">試験(<?php echo $type_count_result[6]["cnt"]+$type_count_result[7]["cnt"] ?>)</li>
                        <li class="marginbar" id = "インターン" value="インターン">インターン(<?php echo $type_count_result[11]["cnt"]+$type_count_result[12]["cnt"] ?>)</li>
                    </ul>


                <?php //sqlで取得した全学生の最近のレポートを一件ずつ表示
                    for($i = 0;$i<15 && $report_fromEveryone = $stmt2->fetch(PDO::FETCH_ASSOC);$i++):
                        $date=new Datetime($report_fromEveryone['POSTDATE']);
                        $report_date=date_format($date,'Y年m月d日');
                    ?>

                    <div class = "newreports">
                        <ul class="a1">
                            <!--種類--><li class="w2"><p class="reptype"><?php echo $report_fromEveryone['REPTYPE']?></p></li>  
                        </ul>
                        <ul class="a2">
                            <!--text--><li class="w2" id = "str">投稿者:</li>
                            <!--学科--><li class="w2"><p class="department">　<?php echo $report_fromEveryone['DEPARTMENT'];?>　</p></li>
                            <!--氏名--><li class="w2"><p class="studentname"><?php echo $report_fromEveryone['STUDENTNAME']?></p></li>
                            <div class ="reportdate">
                            <!--日付--><li class="w2"><p class="reportdate"><?php echo $report_date ?></p></li>
                            </div>
                        </ul>            
                    <!--内容----><p class="report_impression"><?php echo $report_fromEveryone['IMPRESSION']?></p>
                    </div>
                <?php endfor; //全学生の最近のレポート表示終了?>
        </main>
        <footer></footer>
        <script>var companyCode = <?php echo $str ?>;</script>
        <script src="../script/search/script.js"></script>
        <script src="../script/common/autocomp_ajax.js"></script>
        <script src="../script/common/modal.js"></script>
    </body>
</html>
