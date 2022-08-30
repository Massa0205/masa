<?php

    session_start();
    
    /*
    *   ログインチェック
    */
    if(!isset($_SESSION["login"])){
        header("Location: ../login/");
    }

    require_once("../functions/common/function.php");
    require_once("../functions/common/connectdb.php");


    /**
     * 
     * 教師ページにアクセスする権限チェック
     * なければメッセージ表示
     * 
     */
    if(!security::isTeacher($_SESSION["auth"])){        
        header("Location: ../");
    }

    $pdo = connectDb();

    $report_admit_status = 0;
    $teacher_id = $_SESSION["login"];

    if(security::isTeacherOfClass($_SESSION["auth"])){
        $report_admit_status = 0;
        $sql = ("SELECT a.waiting_number
                            ,d.name AS rep_name
                            ,a.report_date
                            ,b.name
                            ,c.company_name
                            ,a.impression 
                        FROM report_waiting_admit a 
                            JOIN students b
                                ON a.poster = b.student_number 
                                    AND b.teacher = '".$teacher_id."'  
                                        AND admit_status = ? 
                            JOIN companies c 
                                ON a.company_code = c.company_code
                            JOIN report_type d 
                                ON a.type = d.id");
    }
    else if(security::isTeacherOfFindWork($_SESSION["auth"])){
        $report_admit_status = 1;
    }
    else if(security::isTeacherOfAll($_SESSION["auth"])){
        // 
        $report_admit_status = 2;
    }
    else if(security::isTeacherOfOffice($_SESSION["auth"])){
        $report_admit_status = 3;
    }
    else if(security::isBoss($_SESSION["auth"])){
        $report_admit_status = 4;

    }
    $sql = ('SELECT a.waiting_number
                    ,d.name as rep_name
                    ,a.report_date
                    ,b.name
                    ,c.company_name
                    ,a.impression 
                FROM report_waiting_admit a 
                    JOIN students b
                        ON (a.poster = b.student_number) 
                            AND admit_status = ? 
                    JOIN companies c 
                        ON a.company_code = c.company_code
                    JOIN report_type d 
                        ON a.type = d.id');
    /*******要改善******/
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($report_admit_status));
    $res = $stmt->fetchAll();

    
    
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel ="stylesheet" href="../css/common/reset.css">
    <link rel ="stylesheet" href="../css/teacher/style.css">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <title>DenBAdmin</title>
</head>
<body>
    <header><!--ヘッダー-->
        <div class="header-contents">
            <div class="header-logo"><a href="../">DenB</a><!--ロゴ--></div>
            <div class="header-search">
                <form class ="frm" name=form1 method = "get" action = "search/">
                    <input type = "text" name = "name" id="search-company" placeholder="企業名から探す...">
                    <button class="btn" type="admit"><i class="fas fa-search mysearch"></i></button>
                </form>
            </div>
            <div class="header-list">
                <ul class="header-ul">
                    <?php if(security::isStudent($_SESSION["auth"])){  ?><li class = "topleftbar" id = "uploadbutton">投稿</li><?php } ?>
                    <?php if(security::isAdmin($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href="#">管理者</a></li><?php } ?>
                    <?php if(security::isTeacher($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href = "../teacher/">教師専用</a></li><?php } ?>
                    <li class = "topleftbar"><a class="btnrightbar" href = "../account/">アカウント</a></li>

                </ul>
            </div>
        </div>
    </header><!--ヘッダー-->
    <main>
        <div class="menu">
            <ul class="mode-items">
                <div class="items">
                    <li id="selected"><a href="#">承認待ちデータ</a></li>
                    <li class = "mode" id="notselectefd"><a href="./datamanage/">データ管理</a></li>
                </div>
            </ul>
        </div>
        <div class="contents">
        <?php
            foreach($res as $res):
                ?>
            <div class="box">
                <div class="report">
                    <ul>
                        <li><span>受験日:　</span><?php echo $res["report_date"] ?></li>
                        <li><span>投稿者:　</span><?php echo $res["name"]?></li>
                        <li><span>企業名:　</span><?php echo $res["company_name"] ?></li>
                        <li><span>種類　:　</span><?php echo $res["rep_name"] ?></li>                        
                        <li><?php echo $res["impression"]?></li>
                        <li>
                            <div class="subbtn">
                                <button type="submit" id="admit" class="admit-btn" value="<?php echo $res["waiting_number"] ?>" >承認</button>
                                <button type="submit" id="deny" class="deny-btn" value="<?php echo $res["waiting_number"] ?>">却下</button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <?php 
            endforeach;                 
            ?>
        </div>

    </main>
    <script>
        $('.admit-btn').click(function(){
            var status = <?php echo $report_admit_status ?>;
            var result = window.confirm("承認してもよろしいですか？");
            if(result == false){
                return 0;
            }
            $.ajax({
                url: "../functions/common/transaction.php",
                type: "POST",
                cache: false,
                data:{
                    number:$(this).attr("value"),type:"add",currentStatus:status
                },
                success:function(){
                    console.log("ok");
                },
                error:function(){
                    console.log("error")
                }
            })
        });
        $('.deny-btn').click(function(){
            var elm = $(this).parents('.box');
            var id = $(this).attr("value");
            var result = window.confirm("却下してもよろしいですか？");
            if(result == false){
                return 0;
            } 
            $.ajax({
                url: "../functions/common/transaction.php",
                type: "POST",
                cache: false,
                data:{
                    number:$(this).attr("value"),type:"remove"
                },
                success:function(){
                    elm.fadeOut();
                },
                error:function(){
                }
            })
        });
    </script>
</body>
</html>