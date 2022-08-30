<?php 

    session_start();
    /*
        ログインチェック
    */
    if(!isset($_SESSION["login"])){
        header("Location: login/");
    }
    /*
        DB接続メソッド
        Ajaxメソッド
        読み込み
    */
    require_once("functions/common/connectdb.php");
    require_once("functions/common/function.php");

    //pdo受け取り
    
    $pdo = connectDb();
    //DB接続エラーのときはエラー画面に遷移
    if(!isset($pdo)){
        header("Location: /error/");
    }
    /*
        全学科の直近の報告書取得
    */
    $allDepartmentReports = $pdo->query('SELECT REP.company_code AS REPORT_COMPANY_ID
                                                ,REP.poster AS REPORT_POSTER
                                                ,REP.report_date AS report_date
                                                ,REP.impression AS REPORT_IMPRESSION
                                                ,COM.company_name AS REPORT_COMPANY_NAME
                                                ,STU.name AS REPORT_STUDENT_NAME
                                                ,STU.department AS REPORT_STUDENT_DEPARTMENT
                                                ,TYP.name AS REPORT_TYPE 
                                            FROM reports REP 
                                                JOIN companies COM 
                                                    ON REP.company_code = COM.company_code
                                                JOIN students STU 
                                                    ON REP.poster = STU.student_number
                                                JOIN report_type TYP ON REP.type = TYP.id 
                                            ORDER BY REP.report_date DESC');

    /*
        ログインユーザーの所属学科の直近の報告書取得
    */
    $belongDepartmentReports = $pdo->prepare('SELECT REP.company_code AS REPORT_COMPANY_ID
                                                    ,REP.poster AS REPORT_POSTER
                                                    ,REP.report_date AS report_date
                                                    ,REP.impression AS REPORT_IMPRESSION
                                                    ,COM.company_name AS REPORT_COMPANY_NAME
                                                    ,STU.name AS REPORT_STUDENT_NAME
                                                    ,STU.department AS REPORT_STUDENT_DEPARTMENT
                                                    ,TYP.name AS REPORT_TYPE
                                                FROM reports REP 
                                                    JOIN companies COM 
                                                        ON REP.company_code = COM.company_code 
                                                    JOIN students STU 
                                                        ON REP.poster = STU.student_number
                                                            AND STU.department = ?
                                                    JOIN report_type TYP   
                                                        ON REP.type = TYP.id 
                                                ORDER BY REP.report_date DESC');
    $belongDepartmentReports->execute(array($_SESSION["UserDepartment"]));

    /*
        ログインしているユーザーの権限によって表示制限数切り替え
        デフォルト:15件
        生徒:5件
    */  
    $reportListMaxNum = 15;
    $studentFlg = false;
    if(security::isStudent($_SESSION["auth"])){
        $reportListMaxNum = 5;
        $studentFlg = true;
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
                <?php 
                while($allDepartmentReportsRow = $allDepartmentReports->fetch(PDO::FETCH_ASSOC)): //行取り出しループ
                    for($counter = 0;$counter < $reportListMaxNum;$counter += 1): //表示件数制限
                        $work_date=new Datetime($allDepartmentReportsRow['report_date']);
                        $reportDate=date_format($work_date,'Y年m月d日');
                        $reportCompanyName = $allDepartmentReportsRow['REPORT_COMPANY_NAME'];
                        $reportType = $allDepartmentReportsRow['REPORT_TYPE'];
                        $reportDepartment = $allDepartmentReportsRow['REPORT_STUDENT_DEPARTMENT'];
                        $reportPoster = $allDepartmentReportsRow['REPORT_STUDENT_NAME'];
                        $reportImpression = $allDepartmentReportsRow['REPORT_IMPRESSION'];
                ?>
                <div class = "newreports">
                    <ul class="a1">
                        <!--企業-->
                        <li class="w1"><a class="companyname" id = "<?php echo $reportCompanyName ?>"><?php echo $reportCompanyName ?></a></li>
                        <!--種類-->
                        <li class="w2"><p class="reptype"><?php echo $reportType ?></p></li>  
                    </ul>
                    <ul class="a2">
                        <li class="w2" id="str">投稿者:</li>
                        <!--学科-->
                        <li class="w2"><p class="department"><?php echo $reportDepartment ?></p></li>
                        <!--氏名-->
                        <li class="w2"><p class="studentname"><?php echo $reportPoster ?></p></li>
                        <div class ="reportdate">
                            <!--日付-->
                            <li class="w2"><p class="reportdate"><?php echo $reportDate ?></p></li>
                        </div>
                    </ul>            
                <!--内容----><p class="report_impression"><?php echo $reportImpression ?></p>
                </div>
                <?php
                    endfor;
                    break;
                endwhile;
                ?>

            <div class="kuuhaku"></div>

            <!--ユーザーの学科の最近のレポート-->
            <?php
            /*
                所属学科の直近の報告書は、ログインユーザーが生徒の場合のみ表示
            */
            if($studentFlg):
            ?>
            <h2 class = "recentlyreport">あなたの学科の最近のレポート</h2>
            <?php 
                /*
                    報告書を1行取り出しループ
                */
                while($belongDerpartmentReportsRow = $belongDepartmentReports->fetch(PDO::FETCH_ASSOC)):
                    /* 最大5件 */
                    for($counter = 1;$counter <= 5; $counter += 1):
                        $date2               = new Datetime($belongDerpartmentReportsRow['report_date']);
                        $reportDate          = date_format($date2,'Y年m月d日');
                        $reportCompany = $belongDerpartmentReportsRow['REPORT_COMPANY_NAME'];
                        $reportType         = $belongDerpartmentReportsRow['REPORT_TYPE'];
                        $reportDepartment   = $belongDerpartmentReportsRow['REPORT_STUDENT_DEPARTMENT'];
                        $reportPoster  = $belongDerpartmentReportsRow['REPORT_STUDENT_NAME'];
                        $reportImpression   = $belongDerpartmentReportsRow['REPORT_IMPRESSION'];
            ?>

                <div class = "newreports">
                    <ul class="a1">
                            <!--企業--><li class="w1"><a class="companyname" id = "<?php echo $reportCompany?>"><?php echo $reportCompany ?></a></li>
                            <!--種類--><li class="w2"><p class="reptype"><?php echo $reportType ?></p></li>  
                        </ul>
                        <ul class="a2">
                            <!--text--><li class="w2" id="str">投稿者:</li>
                            <!--学科--><li class="w2"><p class="department"><?php echo $reportDepartment ?></p></li> 
                            <!--氏名--><li class="w2"><p class="studentname"><?php echo $reportPoster ?></p></li>

                            <div class ="reportdate">
                            <!--日付--><li class="w2"><p class="reportdate"><?php echo $reportDate?></p></li>
                            </div>
                    </ul>                 
                <!--内容--><p class="report_impression"><?php echo $reportImpression ?></p>
                </div>
            <?php 
                    endfor;
                    break;
                endwhile;
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

