<?php


function updateBlackList()
{
    $blacklist =  __DIR__ . '/../files/blacklist.txt';

    file_put_contents($blacklist , ip2long(getRealIP()) . ',', FILE_APPEND | LOCK_EX);
}


function getRealIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];

    //whether ip is from proxy
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];

    //whether ip is from remote address
    return $_SERVER['REMOTE_ADDR'];

}