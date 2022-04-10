<?php
    if(isset($_POST["type"])==false){
        header("Location : ../");
        exit();
    }
    require_once("../common/connectdb.php");
    $pdo = connectDb();

    $com = $_POST["company"];
    $ty = $_POST["type"];
    if($ty == "alltype"){//報告書のタイプ別か否か
        $stmt = $pdo->prepare("SELECT A.poster AS POSTER,DATE_FORMAT(A.report_date, '%Y年%m月%d日')  AS POSTDATE,A.impression AS IMPRESSION,B.company_name AS COMPANYNAME,C.name AS STUDENTNAME,C.department AS DEPARTMENT,D.name AS REPTYPE 
                                FROM reports A JOIN companies B ON A.company_code = B.company_code AND A.company_code = ?
                                JOIN students C ON A.poster = C.student_number 
                                JOIN report_type D ON A.type = D.id
                                ORDER BY A.report_date ASC");
        $stmt->execute(array($com));
    }
    else{
        $stmt = $pdo->prepare("SELECT A.poster AS POSTER,DATE_FORMAT(A.report_date, '%Y年%m月%d日')  AS POSTDATE,A.impression AS IMPRESSION,B.company_name AS COMPANYNAME,C.name AS STUDENTNAME,C.department AS DEPARTMENT,D.name AS REPTYPE 
                                    FROM reports A JOIN companies B ON A.company_code = B.company_code AND A.company_code = ?
                                    JOIN students C ON A.poster = C.student_number 
                                    JOIN report_type D ON A.type = D.id AND D.name like ?
                                    ORDER BY A.report_date ASC");
        $stmt->execute(array($com,"%".$ty."%"));
    }
    if($pdo)
    while($data = $stmt->fetch()){
        $array[]=array(
            'poster'        =>  $data["POSTER"],
            'POSTDATE'      =>  $data["POSTDATE"],
            'IMPRESSION'    =>  $data["IMPRESSION"],
            'COMPANYNAME'   =>  $data["COMPANYNAME"],
            'STUDENTNAME'   =>  $data["STUDENTNAME"],
            'DEPARTMENT'    =>  $data["DEPARTMENT"],
            'REPORTTYPE'    =>  $data["REPTYPE"]

        );
    }
    echo json_encode($array,JSON_UNESCAPED_UNICODE);
?>