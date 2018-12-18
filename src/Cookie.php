<?php

namespace App;

class Cookie
{
    public function setCookie($name, $value = "", $expire = 0, $path = "",
                              $domain = "", $secure = false, $httponly = false)
    {
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    public function getCookie(string $name): string
    {
        if (!empty($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return "";
    }

    public function deleteCookie(string $name)
    {
        unset($_COOKIE[$name]);
        setcookie($name, '', time() - 3600);
    }
}