<?php
if(!isset($_POST['param'])){
    header("Location:../../");
}
require_once("../../functions/common/connectdb.php");
require_once("../../functions/common/function.php");
$pdo=connectDb();
/******教師配列、チェックボックス配列受け取り*********/
foreach($_POST['param'] as $result){
    $stmt = $pdo->prepare('UPDATE accounts SET authority = ? WHERE id = ?');
    $stmt->execute(array($result['value'],$result['id']));
}

?>
