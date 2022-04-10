<?php 
class security{

    const BOSS = 0x80;
    const TEACHEROFOFFICE = 0x40;
    const TEACHEROFALL = 0x20;
    const TEACHEROFFINDWORK = 0x10;
    const TEACHEROFCLASS = 0x8;
    const ADMIN = 0x4;
    const TEACHER = 0x2;
    const STUDENT = 0x1;
    const TEACHER_ADMIN = self::ADMIN | self::TEACHER;

    static function isStudent($auth){
        return !!($auth & self::STUDENT);
    }
    static function isAdmin($auth){
        return !!($auth & self::ADMIN);
    }

    /**
     * 教員権限を持っているか判定する
     * 
     * @param int $auth 判定するセキュリティレベル
     * 
     * @return bool 許可・不許可
     */
    static function isTeacher($auth){
        return !!($auth & self::TEACHER);
    }
    static function isTeacherOfClass($auth){
        return !!($auth & self::TEACHEROFCLASS);
    }
    static function isTeacherOfFindWork($auth){
        return !!($auth & self::TEACHEROFFINDWORK);
    }
    static function isTeacherOfAll($auth){
        return !!($auth & self::TEACHEROFALL);
    }
    static function isTeacherOfOffice($auth){
        return !!($auth & self::TEACHEROFOFFICE);
    }
    static function isBoss($auth){
        return !!($auth & self::BOSS);
    }
}
/******* 
 * 0000|0010 2
 * 0000|0100 4
 * 0000|1000 8
 * 0001|0000 16
 * 0001|0100 20
 *64 32 16 8 4 2 1 
 * 
 * 
 * 
 * 
*/
