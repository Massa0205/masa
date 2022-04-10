<?php 

   // session_cache_limiter('private_no_expire');
    session_start();
    //ログインできていない場合はログイン画面に戻る
    if(!isset($_SESSION["login"])){
        header("Location: login/");
    }
    /*******関数読み込み　要編集**** */
    require_once("functions/common/connectdb.php");
    require_once("functions/common/function.php");

    //pdo受け取り
    
    $pdo = connectDb();
    //DB接続エラーのときはエラー画面に遷移
    if(!isset($pdo)){
        header("Location: /error/");
    }
    //全学科最近のレポートSQL発行
    $sqlresult_alldep = $pdo->query('SELECT REP.company_code AS REPORT_COMPANY_ID,REP.poster AS REPORT_POSTER,REP.report_date AS REPORT_DATE,REP.impression AS REPORT_IMPRESSION,COM.company_name AS REPORT_COMPANY_NAME,STU.name AS REPORT_STUDENT_NAME,STU.department AS REPORT_STUDENT_DEPARTMENT,TYP.name AS REPORT_TYPE 
                                        FROM reports REP JOIN companies COM ON REP.company_code = COM.company_code
                                        JOIN students STU ON REP.poster = STU.student_number
                                        JOIN report_type TYP ON REP.type = TYP.id 
                                        ORDER BY REP.report_date DESC;');

    //ユーザーの学科最近のレポートSQL発行
    $sqlresult_userdep=$pdo->prepare('SELECT REP.company_code AS REPORT_COMPANY_ID,REP.poster AS REPORT_POSTER,REP.report_date AS REPORT_DATE,REP.impression AS REPORT_IMPRESSION,COM.company_name AS REPORT_COMPANY_NAME,STU.name AS REPORT_STUDENT_NAME,STU.department AS REPORT_STUDENT_DEPARTMENT,TYP.name AS REPORT_TYPE
                                        FROM reports REP JOIN companies COM ON REP.company_code = COM.company_code 
                                        JOIN students STU ON REP.poster = STU.student_number
                                        AND STU.department = ?
                                        JOIN report_type TYP ON REP.type = TYP.id 
                                        ORDER BY REP.report_date DESC');
    $sqlresult_userdep->execute(array($_SESSION["UserDepartment"]));

    //ログインしているユーザーの権限によって表示数を変える
    $output_limit = 15;
    if(security::isStudent($_SESSION["auth"])){
        $output_limit = 5;  
    }

?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "utf-8">
        <title>DenBトップ</title>
        <link rel="stylesheet"  href="/css/common/reset.css">
        <link rel="stylesheet"  href="/css/common/header.css">        
        <link rel="stylesheet"  href="/css/common/autocomp.css">
        <link rel="stylesheet"  href="/css/top/style.css">
        <link rel="stylesheet"  href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" >
        <link rel="stylesheet"  href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>
    <body>
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
        <div class = "bodyy">
        <?php /*ヘッダー読み込み*/ include './functions/common/header.php'?>
        <main>
            <!--全学生の最近のレポート---->
            <h1 class = "recentlyreport">電ビ生の最近のレポート</h1>
                <?php //sqlで取得した全学生の最近のレポートを一件ずつ表示
                for($i = 0;$i<$output_limit && $report_fromEveryone = $sqlresult_alldep->fetch(PDO::FETCH_ASSOC);$i++):
                    $work_date=new Datetime($report_fromEveryone['REPORT_DATE']);
                    $report_date=date_format($work_date,'Y年m月d日');
                    $report_company_name = $report_fromEveryone['REPORT_COMPANY_NAME'];
                    $report_type = $report_fromEveryone['REPORT_TYPE'];
                    $report_poster_dep = $report_fromEveryone['REPORT_STUDENT_DEPARTMENT'];
                    $report_poster_name = $report_fromEveryone['REPORT_STUDENT_NAME'];
                    $report_impression = $report_fromEveryone['REPORT_IMPRESSION'];
                ?>
                <div class = "newreports">
                    <ul class="a1">
                        <!--企業-->
                        <li class="w1"><a class="companyname" id = "<?php echo $report_company_name ?>"><?php echo $report_company_name ?></a></li>
                        <!--種類-->
                        <li class="w2"><p class="reptype"><?php echo $report_type ?></p></li>  
                    </ul>
                    <ul class="a2">
                        <li class="w2" id="str">投稿者:</li>
                        <!--学科-->
                        <li class="w2"><p class="department"><?php echo $report_poster_dep ?></p></li>
                        <!--氏名-->
                        <li class="w2"><p class="studentname"><?php echo $report_poster_name ?></p></li>
                        <div class ="reportdate">
                            <!--日付-->
                            <li class="w2"><p class="reportdate"><?php echo $report_date ?></p></li>
                        </div>
                    </ul>            
                <!--内容----><p class="report_impression"><?php echo $report_impression ?></p>
                </div>
            <?php endfor; //全学生の最近のレポート表示終了?>

            <div class="kuuhaku"></div>

            <!--ユーザーの学科の最近のレポート-->
            <?php 
            if(security::isStudent($_SESSION["auth"])):?>
            <h2 class = "recentlyreport">あなたの学科の最近のレポート</h2>
            <?php  //SQLで取得したユーザーの学科のレポートを一件ずつ表示
                for($i = 0; $i < 5 && $report_fromClassmate = $sqlresult_userdep->fetch(PDO::FETCH_ASSOC);$i++):
                    $date2=new Datetime($report_fromClassmate['REPORT_DATE']);
                    $report_date=date_format($date2,'Y年m月d日');
                    $report_company_name = $report_fromEveryone['REPORT_COMPANY_NAME'];
                    $report_type = $report_fromEveryone['REPORT_TYPE'];
                    $report_poster_dep = $report_fromEveryone['REPORT_STUDENT_DEPARTMENT'];
                    $report_poster_name = $report_fromEveryone['REPORT_STUDENT_NAME'];
                    $report_impression = $report_fromEveryone['REPORT_IMPRESSION'];
                ?>

                <div class = "newreports">
                    <ul class="a1">
                            <!--企業--><li class="w1"><a class="companyname" id = "<?php echo $report_company_name?>"><?php echo $report_company_name ?></a></li>
                            <!--種類--><li class="w2"><p class="reptype"><?php echo $report_type ?></p></li>  
                        </ul>
                        <ul class="a2">
                            <!--text--><li class="w2" id="str">投稿者:</li>
                            <!--学科--><li class="w2"><p class="department"><?php echo $report_poster_dep ?></p></li> 
                            <!--氏名--><li class="w2"><p class="studentname"><?php echo $report_poster_name ?></p></li>

                            <div class ="reportdate">
                            <!--日付--><li class="w2"><p class="reportdate"><?php echo $report_date?></p></li>
                            </div>
                    </ul>                 
                <!--内容--><p class="report_impression"><?php echo $report_impression ?></p>
                </div>
            <?php 
                endfor;
            endif;
            ?>
        </main>
        <div class="footermargin">
            
        </div>

        </div>
        <script src="./script/top/script.js"></script>
        <script src="./script/common/modal.js"></script>
    </body>
</html>

