<?php
    session_start();
    if(!!!isset($_SESSION["login"])){//セッションIDが発行されていない場合はloginページに戻る
        header("Location: ../../login/");
    }
    require_once("../../functions/common/connectdb.php");
    require_once("../../functions/common/function.php");
    $pdo = connectDb();

    if(!security::isTeacher($_SESSION["auth"])){
        header("Location: ../../");
    }
    $statement = $pdo->prepare("SELECT 
                                    ST.student_number AS student_number
                                    ,ST.name AS NAME
                                    ,AC.password AS password
                                    ,IFNULL(SUB.DAT,'活動記録なし') AS date
                                    FROM students ST
                                        INNER JOIN accounts AC
                                            ON ST.teacher = ?
                                                AND ST.student_number = AC.id
                                        LEFT OUTER JOIN (SELECT 
                                                            DATE_FORMAT(report_date,'%Y年%m月%d日')AS DAT
                                                            ,poster 
                                                                FROM reports 
                                                                GROUP BY poster 
                                                                ORDER BY poster) SUB 
                                            ON AC.id = SUB.poster;");
    $statement->execute(array($_SESSION["login"]));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel ="stylesheet" href="../../css/common/reset.css">
    <link rel ="stylesheet" href="../../css/teacher/datamanage3.css">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
    <title>DenBAdmin</title>
</head>
<body>
    <!--------------モーだrウィンドウーーーーーーーー------------->
    <div class="modal-wrapper">
        <div class="modal-contents">
            <div class="block-1">
                <div class="student-info">
                    <h2 class="modal-student-name">あいうえお</h2>
                    <p class="label-first-date"></p>
                </div>
                <div class="student-graph">
                    <div class="chart-area">
                        <canvas id="myPieChart"></canvas>
                    </div>
                </div>
            </div>
            <!--報告書--->
            <div class="block-2">

                <div class="student-reports">
                    <!--ajax通信した報告書データ-->
                </div>
                
            </div>
        </div>
    </div>
    <!------------モーダル終了------------------------------------------->
    <bodyy>
        <header><!--ヘッダー-->
            <div class="header-contents">
                <div class="header-logo"><a href="/">DenB</a><!--ロゴ--></div>
                <div class="header-search">
                    <form class ="frm" name=form1 method = "get" action = "../../search/">
                        <input type = "text" name = "name" id="search-company" placeholder="企業名から探す...">
                        <button class="btn" type="admit"><i class="fas fa-search mysearch"></i></button>
                    </form>
                </div>
                <div class="header-list">
                    <ul class="header-ul">
                        <?php if(security::isStudent($_SESSION["auth"])): ?>
                            <li class="topleftbar"><a class="btnrightbar" href="upload/" >投稿</a></li>
                        <?php endif; ?>
                        <li class="topleftbar"><a class="btnrightbar" href="../">教師専用</a></li>
                        <li class="topleftbar"><a class="btnrightbar" href="../../account/">アカウント</a></li>
                    </ul>
                </div>
            </div>
        </header><!--ヘッダー-->
        
        <main>
            <div class="menu">
                <ul class="mode-items">
                    <div class="items">
                        <li id="notselected"><a href="/teacher/">承認待ちデータ</a></li>
                        <li class = "mode" id="selected"><a href="">データ管理</a></li>
                    </div>
                </ul>
            </div>
            <div class="contents">
                <div class="dummy"></div>
                <table border="1">
                    <tr id="col">
                        <th>学籍番号</th>
                        <th>氏名</th>
                        <th>最後の活動日</th>
                    </tr>
                    <?php while($row = $statement->fetch()): ?>
                    <tr class="rows" id="<?php echo $row["student_number"] ?>">
                        <!--番号--><td class="stu"><?php  echo $row["student_number"] ?></td>
                        <!--氏名--><td class="stu" id="student-name"><?php  echo $row["NAME"] ?></td>
                        <!--パス--><td class="pass" name="<?php echo $row['password']?>"><?php echo $row['date']?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
                <div class="dummy"></div>
            </div>
        </main>
        <script src="../../script/teacher/script.js"></script>
    </bodyy>

</body>
</html>