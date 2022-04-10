<?php
    session_start();
    require_once('../../functions/common/function.php');
    require_once('../../functions/common/connectdb.php');
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
    <meta charset="UTF-8">
    <title>Admin Top</title>
    <link rel="stylesheet"  href="../../css/common/reset.css">
    <link rel="stylesheet"  href="../../css/common/header.css">
    <link rel="stylesheet"  href="../../css/common/autocomp.css">
    <link rel="stylesheet"  href="../../css/common/adminheader.css">
    <link rel="stylesheet"  href="../../css/admin/top/style.css">
    <link rel="stylesheet"  href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" >
    <link rel="stylesheet"  href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" >
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
    <?php include '../../functions/common/header.php' ?>
    <div class="left-bar">
        <ul class="left-bar-list">
            <li class="left-bar-notselected" id="left-bar-btn-top">トップ</li>
            <li class="left-bar-selected"    id="left-bar-btn-teacherlist">教師権限管理</li>
            <li class="left-bar-notselected" id="left-bar-btn-studentlist">生徒管理</li>
            <li class="left-bar-notselected" id="left-bar-btn-reportslist">報告書管理</li>
            <li class="left-bar-notselected" id="left-bar-btn-companylist">企業管理</li>
        </ul>
    </div>
    <?php /*
    <div class="contents" >
        <p>サーバー稼働時間 <span class="last-date">3日14時間15分</span></p>
        <p>最後のバックアップ <span class="last-date">2022年2月10日</span></p>
        <button>今すぐバックアップを取る</button>
    </div>
    */
    ?>
    <div class="contents">
        <div class="buttons">
            <p class="btn-change" id="btn-change-notclicked">変更する</p>
        </div>
        <table>
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
                    <th><input class="check-box" value="<?php echo security::ADMIN; ?>"      disabled type="checkbox" <?php if(security::isAdmin($result['t_AUTH'])){ ?> checked = "checked"<?php } ?>></th>
                    <th><input class="check-box" value="<?php echo security::TEACHEROFCLASS; ?>"     disabled type="checkbox" <?php if(security::isTeacherOfClass($result['t_AUTH'])){ ?> checked = "checked"<?php }?>></th>
                    <th><input class="check-box" value="<?php echo security::TEACHEROFFINDWORK; ?>"  disabled type="checkbox" <?php if(security::isTeacherOfFindWork($result['t_AUTH'])){ ?> checked="checked"<?php } ?>></th>
                    <th><input class="check-box" value="<?php echo security::TEACHEROFALL; ?>"       disabled type="checkbox" <?php if(security::isTeacherOfAll($result['t_AUTH'])){ ?> checked="checked"<?php } ?>></th>
                    <th><input class="check-box" value="<?php echo security::TEACHEROFOFFICE; ?>"    disabled type="checkbox" <?php if(security::isTeacherOfOffice($result['t_AUTH'])){ ?> checked="checked"<?php } ?>></th>
                    <th><input class="check-box" value="<?php echo security::BOSS; ?>"               disabled type="checkbox" <?php if(security::isBoss($result['t_AUTH'])){ ?> checked="checked"<?php } ?>></th>
                </tr>        
            <?php endforeach; ?>
            <script>console.log(<?php echo security::ADMIN; ?>);</script>
        </table>
    </div>
    <script src="../../script/common/autocomp_ajax.js"></script>
    <script src="../../script/admin/script.js"></script>
</body>
</html>