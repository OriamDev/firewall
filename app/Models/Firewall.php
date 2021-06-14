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


        $this->formatBytes(memory_get_peak_usage());

    }

    private function formatBytes($bytes, $precision = 2)
    {

        $units = array("b", "kb", "mb", "gb", "tb");

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));
        $RAM = round($bytes, $precision) . " " . $units[$pow];
       if (DEBUG)
           r($RAM);
    }


    private function checkRequest()
    {
        if (DEBUG)
            r('checkRequest');


        if(!isset($_SESSION[$this->ip]))
        {
            $newIpRequestWithTime = [1, time()];
            $_SESSION[$this->ip] = $newIpRequestWithTime;

            if(DEBUG)
                r($newIpRequestWithTime);
        }


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
            die();
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

            $fastRequest = ($_SESSION[$this->ip][1] > time() - RULES['interval']);

            if($fastRequest && $_SESSION[$this->ip][0] < RULES['maxRequests'])
                $_SESSION[$this->ip][0]++;

            elseif ($fastRequest)
                $this->captchaGenerate();

            else
                $_SESSION[$this->ip] = [1, time()];



    }



}