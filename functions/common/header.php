<?php 
    /*ヘッダーファイル*/
    session_start();
    //ログインできていない場合はログイン画面に戻る
    if(!isset($_SESSION["login"])){
        header("Location: login/");
    }
?>
<header><!--ヘッダー-->
    <div class="header-contents">
        <div class="header-logo"><a href="">DenB</a><!--ロゴ--></div>
        <div class="header-search">
            <form class ="frm" name=form1 method = "get" action = "/search/">
                <input type = "text" name = "name" class = "search-company" id="search-company" placeholder="企業名から探す...">
                <button class="btn" type="admit"><i class="fas fa-search mysearch"></i></button>
            </form>
        </div>
        <div class="header-list">
            <ul class="header-ul">
                <?php if(security::isStudent($_SESSION["auth"])){   ?><li class = "topleftbar" id = "uploadbutton">投稿</li><?php } ?>
                <?php if(security::isAdmin($_SESSION["auth"])){     ?><li class = "topleftbar"><a class="btnrightbar" href="./administrator/">管理者</a></li><?php } ?>
                <?php if(security::isTeacher($_SESSION["auth"])){   ?><li class = "topleftbar"><a class="btnrightbar" href = "teacher/">教師専用</a></li><?php } ?>
                <li class = "topleftbar"><a class="btnrightbar" id="acc" href = "./account/">アカウント</a></li>
            </ul>
        </div>
    </div>
    <div class="hover-window">
        <a class="logout-btn" href="../../logout/">ログアウト</a>
    </div>
</header>