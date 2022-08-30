<?php
    session_start();
    if(isset($_SESSION['login'])==false){//ログインしていない
        header("Location: ../login/");
        exit();
    }

    require_once("../functions/common/connectdb.php");
    $pdo = connectDb();

    if(isset($_POST['company'])){
        $pdostatement=$pdo->prepare('select company_code from companies where company_name = ?');
        $pdostatement->execute(array($_POST['company']));
        $row = $pdostatement->fetchAll();
        if($pdostatement->rowCount()>0){
            $com_name = $row[0]['company_code'];
            $pdostatement1=$pdo->prepare('insert into report_waiting_admit (poster,company_code,type,impression,report_date,admit_status) values (?,?,?,?,?,0)');
            $pdostatement1->execute(array($_SESSION["login"],$com_name,$_POST['reporttype'],$_POST['naiyou'],$_POST['repdate']));
            header("Location: ../");
        }
    }
    if(isset($_POST['flg'])){
        $stmt = $pdo->prepare('INSERT INTO COMPANIES (company_name) VALUES (?)');
        $stmt->execute(array($_POST['company']));
        $stmt = $pdo->prepare('SELECT company_code FROM companies WHERE company_name = ?');
        $stmt->execute(array($_POST['company']));
        $result = $stmt->fetch();
        $com_code = $result['company_code'];
        $stmt=$pdo->prepare('insert into report_waiting_admit (poster,company_code,type,impression,report_date,admit_status) values (?,?,?,?,?,0)');
        $stmt->execute(array($_SESSION["login"],$com_name,$_POST['reporttype'],$_POST['naiyou'],$_POST['repdate']));
        header("Location: ../");
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet"  href="../css/common/reset.css">
    <link rel="stylesheet"  href="../css/add/style.css">
    <link rel="stylesheet"  href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" >
    <link rel="stylesheet"  href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
    <div class="msg">
        <h1>DenBデータベースの品質向上にご協力ください</h1>
        <h2>報告しようとしている企業は現在登録されていません。</h2>
        <div id="sender-companyname">企業名:<?php echo $_POST['company']; ?></div>
        <p class="brr">新たな企業としてデータベースに登録いたしますので、</p>
        <p class="brr">すでに別の名前で登録されていないか右の検索ボックスより再度ご確認ください。</p>
        <p>登録されていた場合は、<a id="denb" href="../">DenBページ</a>より再度投稿してください。</p>
        <p class="mistakes">よくある間違い</p>
        <ul class="mistakes-ul">
            <li class="exp-msg-before">・アルファベットをカタカナで書いてしまった。</li>
            <li class="exp-msg-after">例 正:KIS　誤:ケイアイエス</li>
            <li class="exp-msg-before">・ドットや記号を忘れて書いてしまった。</li>
            <li class="exp-msg-after">例 正:H・I・S　誤:HIS</li>
            <li class="exp-msg-before">・前株後株を間違えて書いてしまった。</li>
            <li class="exp-msg-after">例 正:株式会社藤川　誤:藤川株式会社</li>
        </ul>
    </div>
    <div class="search-box">
        <input type="text" placeholder="企業名を入力して確認..." id="ajaxSearch">
        <div class="company-list">

        </div>
        <input type="submit" class="submit-btn" value="<?php echo $_POST['company'];?>を登録し報告書を投稿する。"disabled>
    </div>
    <script src="../script/addcomp/script.js"></script>
    <script>
        $('.submit-btn').click(function(){
            var post_company     = '<?php echo $_POST['company']?>';
            var post_reporttype  = '<?php echo $_POST['reporttype']?>';
            var post_naiyou      = '<?php echo $_POST['naiyou']?>';
            var post_repdate     = '<?php echo $_POST['repdate']?>';
            $.ajax({
                url: "./",
                    type: "POST",
                    cache: false,
                    dataType:"json",
                    data:{
                        company     : post_company, 
                        reporttype  : post_reporttype,
                        naiyou      : post_naiyou,
                        repdate     : post_repdate,
                        flg         : 'true'
                    },
                success:function(){
                },
                error:function(){
                }
            })
        });
    </script>
</body>
</html>