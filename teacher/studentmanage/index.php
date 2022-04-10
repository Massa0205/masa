<?php
    // session_cache_limiter('private_no_expire');
    session_start();
    if(isset($_SESSION["login"])==false){//セッションIDが発行されていない場合はloginページに戻る
        header("Location: http://localhost/noda/login/");
    }

    /******DB接続処理******/
    try{
        $pdo = new PDO('mysql:host=localhost;dbname=masa', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
    require_once("../../functions/function.php");
    //担任の生徒を問い合わせ
    if(security::isTeacherOfClass($_SESSION["auth"])){
        $stmt2 = $pdo->prepare('SELECT * FROM STUDENTS WHERE TEACHER = ? ORDER BY STUDENT_NUMBER');
        $stmt2->execute(array($_SESSION["login"]));
        $students = $stmt2->fetchAll();
        $student_count = $stmt2->rowCount();
    }
    $stmt3 = $pdo->prepare('select * from teacher');
    $stmt3->execute();
    $teachers = $stmt3->fetchAll();
    $json_students = json_encode($students);
    
?>
<!DOCTYPE html>
<html lang="jp">
    <head>
        <meta charset="UTF-8">
        <link rel ="stylesheet" href="../../css/reset.css">
        <link rel ="stylesheet" href="../../css/teacher/studentmanage.css">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <title>DenBAdmin</title>
    </head>
    <body>
        <header><!--ヘッダー-->
            <div class="header-contents">
                <div class="header-logo"><a href="../../">DenB</a><!--ロゴ--></div>
                <div class="header-search">
                    <form class ="frm" name=form1 method = "get" action = "../../search/">
                        <input type = "text" name = "name" id="search-company" placeholder="企業名から探す...">
                        <button class="btn" type="admit"><i class="fas fa-search mysearch"></i></button>
                    </form>
                </div>
                <div class="header-list">
                    <ul class="header-ul">
                    <?php if(security::isStudent($_SESSION["auth"])){  ?><li class = "topleftbar" id = "uploadbutton">投稿</li><?php } ?>
                    <?php if(security::isAdmin($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href="#">管理者</a></li><?php } ?>
                    <?php if(security::isTeacher($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href = "../../teacher/">教師専用</a></li><?php } ?>
                    <li class = "topleftbar"><a class="btnrightbar" href = "../../account/">アカウント</a></li>
                    </ul>
                </div>
            </div>
        </header><!--ヘッダー-->
        <main>
            <div class="menu">
                <ul class=mode-items>
                    <li class = "mode"><a href="../../teacher/">承認待ちデータ</a></li>
                    <li class = "mode"><a  href="../datamanage/">データ管理</a></li>
                    <li id = "selected"><a  href="#">生徒管理</a></li>
                </ul>
            </div>

                <!--<input class="btnsubmit" id="submitbtn" type="submit" value="承諾">-->

            </form>
        </main>
        <script type="text/javascript" src="nodeClass.js"></script>
        <script>
            $('#left-box-allcheck').click(function(){
                $('input[name="seito[]"]').prop('checked', true);
            })
            $('#left-box-alluncheck').click(function(){
                $('input[name="seito[]"]').prop('checked', false);
            })
            $('#right-box-allcheck').click(function(){
                $('input[name="removeStudent[]"]').prop('checked',true);
            })
            $('#right-box-alluncheck').click(function(){
                $('input[name="removeStudent[]"]').prop('checked',false);
            })
            $('#btn-move').click(function(){
                var listArray = new masaList();
                                //チェックがついている人の値を取得
                var checkedStudent = $('input[name="seito[]"]:checked').map(function(){
                    return $(this).attr("id");
                }).get();
                //すでに右に追加されている人の値を取得
                var alreadyMovedStudent = $('#right-box-ul input').map(function(){
                    console.log($(this).attr("id"));
                    return $(this).attr("id");
                }).get();
                //リストに追加
                for(var i=0;i<checkedStudent.length;i++){
                    listArray.add(checkedStudent[i],$('#'+checkedStudent[i]).val());
                    $('.'+checkedStudent[i]).remove();
                }
                for(var i=0;i<alreadyMovedStudent.length;i++){
                    listArray.add(alreadyMovedStudent[i],$('#'+alreadyMovedStudent[i]).val());
                    $('.'+alreadyMovedStudent[i]).remove();
                }
                var currentItem = listArray.head;
                while(!!currentItem){
                    $('#right-box-ul').append('<li class="'+ currentItem.value +'" name = "checkedstudent"><label for ="'+ currentItem.value +'"><input class="chStudent" type="checkbox" id ="'+ currentItem.value +'" value = "'+ currentItem.name +'" name = "removeStudent[]">' + currentItem.value + currentItem.name +'</label></li>');
                    currentItem = currentItem.next;
                }
            })
            $('#btn-remove').click(function(){
                var listArray = new masaList();

                var checkedStudent = $('input[name="removeStudent[]"]:checked').map(function(){
                    console.log($(this).attr("id"));
                    return $(this).attr("id");
                }).get();
                var peopleInLeftBox = $('#left-box-ul input').map(function(){
                    console.log($(this).attr("id")+'左');
                    return $(this).attr("id");
                }).get();

                for(var i=0;i<checkedStudent.length;i++){
                    listArray.add(checkedStudent[i],$('#'+checkedStudent[i]).val());
                    console.log(checkedStudent[i]+"削除右");
                    $('.'+checkedStudent[i]).remove();
                }
                for(var i=0;i<peopleInLeftBox.length;i++){
                    listArray.add(peopleInLeftBox[i],$('#'+peopleInLeftBox[i]).val());
                    console.log(peopleInLeftBox[i]+"削除左");
                    $('.'+peopleInLeftBox[i]).remove();
                }
                var currentItem = listArray.head;
                while(!!currentItem){
                    $('#left-box-ul').append('<li class="'+ currentItem.value +'" name = "currentstudent"><label for ="'+ currentItem.value +'"><input class="curStudent" type="checkbox" id ="'+ currentItem.value +'" value = "'+ currentItem.name +'" name = "seito[]">' + currentItem.value + currentItem.name +'</label></li>');
                    currentItem = currentItem.next;
                }
            })
            var student_array = <?php echo $json_students;?>;
           $('#submitbtn').click(function(){
               var count = $('input[]')
                window.confirm("")
            });
        </script>
    </body>
</html>