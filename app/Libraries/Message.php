<?php
namespace App\Libraries;

class Message
{
    public static function message($key){
    $key = Hash::makeString($key);
    $message =
    '<div>
    <h1>Correo de Recuperacion de Contraseña</h1>
    <p>click en siguiente enlace para recuperar contraseña</p>

    <a href="http://localhost:3000/newPass?Key='.$key.'">click aqui</a>
    </div>';
    return $message;
}
}
?>