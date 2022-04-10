<?php
    /*if(!isset($_POST["student"])){
        exit();
    }*/
    require_once("../common/connectdb.php");
    $pdo = connectDb();
    //最初の投稿日取得
    $stmt = $pdo->prepare("SELECT DATE_FORMAT(report_date,'%Y年%m月%d日')AS DAT FROM reports WHERE poster = ? LIMIT 1");
    $stmt->execute(array($_POST["student"]));
    $first_date = $stmt->fetch();
    $stmt2 = $pdo->prepare("SELECT COUNT(A.type) AS CNT FROM reports A
                            RIGHT OUTER JOIN report_type B
                            ON A.type = B.id
                            AND A.poster = ?    
                            GROUP BY A.type,B.name
                            ORDER BY B.id");
    $stmt2->execute(array($_POST["student"]));
    $data = $stmt2->fetchAll();
    $cisCnt = (int)$data[0]["CNT"]+(int)$data[1]["CNT"];
    $interviewCnt = (int)$data[2]["CNT"]+(int)$data[3]["CNT"];
    $interview2Cnt = (int)$data[4]["CNT"]+(int)$data[5]["CNT"];
    $examCnt = (int)$data[6]["CNT"]+(int)$data[7]["CNT"]+(int)$data[8]["CNT"]+(int)$data[9]["CNT"];
    $internCnt = (int)$data[11]["CNT"]+(int)$data[12]["CNT"];
    //生徒の報告書取得SQL
    $stmt3 = $pdo->prepare("SELECT B.company_name AS COMNAME,C.name AS TYPENAME,A.impression AS NAIYO ,DATE_FORMAT(A.report_date,'%Y年%m月%d日') AS REPDATE
                    FROM reports A 
                    JOIN companies B
                    ON A.poster = ?
                    AND A.company_code = B.company_code
                    JOIN report_type C
                    ON A.type = C.id
                    ORDER BY A.report_date DESC");
    $stmt3->execute(array($_POST["student"]));

    foreach($stmt3 as $reports){
        $reportsArray[] = array(
            'company_name'=>$reports["COMNAME"],
            'report_type' =>$reports["TYPENAME"],
            'impression'  =>$reports["NAIYO"],
            'reprt_date'  =>$reports["REPDATE"]
        );
    }
    $arr=array(
        'firstdate' =>$first_date["DAT"],
        'cis'       =>$cisCnt,
        'interview' =>$interviewCnt,
        'interview2'=>$interview2Cnt,
        'exam'      =>$examCnt,
        'intern'    =>$internCnt,
        'reports'   =>$reportsArray
    );
    //投稿の種類の割合

    //投稿

    echo json_encode($arr);

?>