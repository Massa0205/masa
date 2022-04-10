<?php
    // session_cache_limiter('private_no_expire');
    session_start();
    if(isset($_SESSION["login"])==false){//セッションIDが発行されていない場合はloginページに戻る
        header("Location: ../login/");
    }

    require_once("../functions/common/function.php");

    /******DB接続処理******/
    try{
        $pdo = new PDO('mysql:host=localhost;dbname=masa', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
    $a = new security;
    if( ! security::isTeacher($_SESSION["auth"])){
        ?><script>window.alert('権限がありません。')</script><?php
        header("Location: ../");

    }
   
    $stmt = $pdo->prepare('select * from accounts where id = ?');
    $stmt->execute(array($_SESSION["login"]));
    $teacher_data = $stmt->fetch();
    $teaID = $_SESSION["login"];

    if(security::isTeacherOfClass($_SESSION["auth"])){
        $curStatus = 0;
        $selectbun = ("select a.waiting_number,d.name as rep_name,a.report_date,b.name,c.company_name,a.impression from report_waiting_admit a join students b
                    on a.poster = b.student_number and b.teacher = '".$teaID."'  and admit_status = 0 
                    join companies c on a.company_code = c.company_code
                    join report_type d on a.type = d.id");
    }
    else if(security::isTeacherOfFindWork($_SESSION["auth"])){
        
        $curStatus = 1;
        $selectbun=('select a.waiting_number,d.name as rep_name,a.report_date,b.name,c.company_name,a.impression from report_waiting_admit a join students b
            on (a.poster = b.student_number)  and admit_status = 1 
            join companies c on a.company_code = c.company_code
            join report_type d on a.type = d.id');

    }
    else if(security::isTeacherOfAll($_SESSION["auth"])){
        // 
        $curStatus = 2;
        $selectbun = ('select a.waiting_number,d.name as rep_name,a.report_date,b.name,c.company_name,a.impression from report_waiting_admit a join students b
        on (a.poster = b.student_number)  and admit_status = 2 
        join companies c on a.company_code = c.company_code
        join report_type d on a.type = d.id');
    }
    else if(security::isTeacherOfOffice($_SESSION["auth"])){
        $curStatus = 3;
        $selectbun = ('select a.waiting_number,d.name as rep_name,a.report_date,b.name,c.company_name,a.impression from report_waiting_admit a join students b
        on (a.poster = b.student_number)  and admit_status = 3 
        join companies c on a.company_code = c.company_code
        join report_type d on a.type = d.id');
    }
    else if(security::isBoss($_SESSION["auth"])){
        $curStatus = 4;
        $selectbun = ('select a.waiting_number,d.name as rep_name,a.report_date,b.name,c.company_name,a.impression from report_waiting_admit a join students b
        on (a.poster = b.student_number)  and admit_status = 4 
        join companies c on a.company_code = c.company_code
        join report_type d on a.type = d.id');
    }
    /*******要改善******/
    $stmt = $pdo->query($selectbun);
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
            foreach($res as $res){
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
            }                 
            ?>
        </div>

    </main>
    <script>
        $('.admit-btn').click(function(){
            var status = <?php echo $curStatus ?>;
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