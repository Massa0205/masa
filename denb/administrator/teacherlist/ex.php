<?php
    session_start();
    require_once('../../functions/common/function.php');
    require_once('../../functions/common/connectdb.php');
    //ログインできていない場合はログイン画面に戻る
    if(!security::isAdmin($_SESSION["auth"])){
        header("Location: ../");
    }



    $pdo=connectDb();

    $stmt = $pdo->query('SELECT T.teacher_id AS t_ID,T.name AS t_NAME,AC.authority AS t_AUTH
                FROM teacher T
                JOIN accounts AC
                ON T.teacher_id = AC.id');

    $result = $stmt->fetchAll();
    
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="../../css/common/reset.css">
    <link rel="stylesheet" href="../../css/admin/teacherlist/style2.css">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <meta charset="UTF-8">
    <title>ADMIN</title>
</head>
<body>
    <header><!--ヘッダー-->
        <div class="header-contents">
            <div class="header-logo"><a href="">DenB Admin</a><!--ロゴ--></div>
            <div class="header-search">
                <form class ="frm" name=form1 method = "get" action = "search/">
                    <input type = "text" name = "name" class = "search-company" id="search-company" placeholder="企業名から探す...">
                    <button class="btn" type="admit"><i class="fas fa-search mysearch"></i></button>
                </form>
            </div>
            <div class="header-list">
                <ul class="header-ul">
                    <?php if(security::isStudent($_SESSION["auth"])):  ?><li class = "topleftbar" id = "uploadbutton">投稿</li><?php endif; ?>
                    <?php if(security::isAdmin($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href="./">管理者</a></li><?php } ?>
                    <?php if(security::isTeacher($_SESSION["auth"])){ ?><li class = "topleftbar"><a class="btnrightbar" href = "../../teacher/">教師専用</a></li><?php } ?>
                    <li class = "topleftbar"><a class="btnrightbar" href = "../../account/">アカウント</a></li>
                </ul>
            </div>
        </div>
    </header><!--ヘッダー-->
    <main>
        <div class="contents">
            <div class="leftbar">
                <ul>
                    <li class="admin-home" >ホーム</li>
                    <li class="teacher-list-button" id="selected">教師リスト</li>
                    <li class="student-list-button">生徒リスト</li>
                    <li class="company-list-button">企業リスト</li>
                    <li class="report-list-button">報告書リスト</li>
                </ul>
            </div>
            <div class="box">
                <div class="buttons">
                    <input type="button" id="btn-change" value="変更する">
                    <input type="button" id="btn-reset"  value="元に戻す">
                    <input type="button" id="btn-submit" value="適用">
                </div>
                <table border="3" bordercolor="black">
                    <tr class="top-label">
                        <th>名前</th>
                        <th>ID</th>
                        <th class="author-box">管理者</th>
                        <th class="author-box">担任</th>
                        <th class="author-box">就職課</th>
                        <th class="author-box">教務部長</th>
                        <th class="author-box">事務局長</th>
                        <th class="author-box">校長</th>
                    </tr>
                    <?php foreach($result as $result): ?>
                        <tr class="teacher-list">
                            <th><?php echo $result['t_NAME']?></th>
                            <th id="teacher-id"><?php echo $result['t_ID'] ?></th>
                            <th><input class="check-box" value="administrator"      disabled type="checkbox" <?php if(security::isAdmin($result['t_AUTH'])){ ?> checked = "checked"<?php } ?>></th>
                            <th><input class="check-box" value="teacherofclass"     disabled type="checkbox" <?php if(security::isTeacherOfClass($result['t_AUTH'])){ ?> checked = "checked"<?php }?>></th>
                            <th><input class="check-box" value="teacheroffindwork"  disabled type="checkbox" <?php if(security::isTeacherOfFindWork($result['t_AUTH'])){ ?> checked="checked"<?php } ?>></th>
                            <th><input class="check-box" value="teacherofall"       disabled type="checkbox" <?php if(security::isTeacherOfAll($result['t_AUTH'])){ ?> checked="checked"<?php } ?>></th>
                            <th><input class="check-box" value="teacherofoffice"    disabled type="checkbox" <?php if(security::isTeacherOfOffice($result['t_AUTH'])){ ?> checked="checked"<?php } ?>></th>
                            <th><input class="check-box" value="boss"               disabled type="checkbox" <?php if(security::isBoss($result['t_AUTH'])){ ?> checked="checked"<?php } ?>></th>
                        </tr>        
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>
    <script>
        $(document).ready(function(){
            var arr = [

            ]
            $('.admin-home').click(function(){
                window.location.href = '../';
            });
            $('.student-list-button').click(function(){
                window.location.href = '../studentlist/';
            });
            $('.company-list-button').click(function(){
                window.location.href = '../companylist/';
            });
            $('.report-list-button').click(function(){
                window.location.href = '../reportlist/';
            });
            $('#btn-change').click(function(){
                $('#btn-submit').show();
                $(this).hide();
                $('.check-box').attr('disabled',false);
            })
            $('#btn-submit').click(function(){
                //先生のリスト
                var indx = 0;
                var array = [];
                $('.teacher-list').each(function(){//教師リストループ
                    var j = 0;
                    var varr = {};                        
                    varr["id"] = $(this).children('#teacher-id').text();
                    $(this).children('th').each(function(){//th要素ループ
                        var elm = $(this).children('.check-box');
                        var flg = false;
                        if(elm.prop('checked')){
                            flg = true;
                        }
                        varr[elm.val()] = flg;
                    });
                    array[indx] = varr;
                    indx += 1;
                });
                console.log(array);
                //ORをとる
                /*var arr = [];
  
                $('[class="check-box"]:checked').each(function(){
                    // 無効化する                
                    // チェックされているの値を配列に格納
                    arr.push($(this).val());
                });
                console.log(arr);
                /********ajax*********/
                /*$.ajax({
                        url : "./ajax.php",
                        type: "POST",
                        data:{  },
                        dataType:"json",
                    success:function(data){
                    },
                    error:function(){
                    }
                })*/
                $('#btn-change').show();
                $(this).hide();
                $('.check-box').attr('disabled',true);
            });
        });


    </script>
</body>
</html>
