<?php


namespace App\Models;


class Firewall
{

    private $ip;

    public function __construct()
    {
        $this->ip = getRealIP();

        //Ip filter module call
        if(! (new IpFilter($this->ip))->check())
            die(); // Drop Requests

        //Crawler Detection
        if(! (new UserAgentFilter($this->ip))->check())
            die(); // Drop Requests

        $this->checkRequest();

    }



    private function checkRequest()
    {
        if(!isset($_SESSION[$this->ip]))
            $_SESSION[$this->ip] = [1, time()];

        elseif (isset($_SESSION['captcha']))
            $this->captchaGenerate();

        else
            $this->checkMaxRequestBySecond();
    }

    private function captchaGenerate()
    {
        if(!isset($_SESSION['url']))
            $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        if($this->checkCaptchaAttempts())
        {
            updateBlackList();
            die('max_captcha_attempts');
        }

        $this->setCaptchaAttempts();

        $_SESSION['captcha'] = simple_php_captcha();
        require __DIR__ .'/../views/error.phtml';
        die();
    }



    private function checkCaptchaAttempts(): bool
    {
        return isset($_SESSION['captcha_attempts']) && $_SESSION['captcha_attempts'] > 4;
    }

    private function setCaptchaAttempts()
    {
        if(!isset($_SESSION['captcha_attempts']))
            $_SESSION['captcha_attempts'] = 1;
        else
            $_SESSION['captcha_attempts'] += 1;
    }

    private function checkMaxRequestBySecond()
    {
        $interval = 1; // segundos
        $maxRequests = 2; // requests
        $fastRequest = ($_SESSION[$this->ip][1] > time() - $interval);

        if($fastRequest && $_SESSION[$this->ip][0] < $maxRequests)
            $_SESSION[$this->ip][0]++;

        elseif ($fastRequest)
            $this->captchaGenerate();

        else
            $_SESSION[$this->ip] = [1, time()];

    }



}