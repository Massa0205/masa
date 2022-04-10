<?php 
    require_once("../connectdb.php");
    $pdo = connectDb();
    $hashedPass = password_hash($_POST["newpass"],PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE accounts SET password = ? WHERE id = ?");
    $stmt->execute(array($hashedPass,$_POST["user"]));

?>