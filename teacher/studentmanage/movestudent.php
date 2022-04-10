<?php
    try{
        $pdo = new PDO('mysql:host=localhost;dbname=masa', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
    $students = $_POST["seito"];
    foreach($students as $students){
        $stmt = $pdo->prepare('update students set teacher = ? where student_number = ?');
        $stmt->execute(array($_POST["teacher-select"],$students));
    }
    header("Location: ../studentmanage");
?>
