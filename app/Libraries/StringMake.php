<?php
namespace App\Libraries;


class StringMake {
    public static function makeString($string = 16 ){
        return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, $string);
    }
    public static function manager_number($string = 6 ){
        return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $string);
    }
}
?>