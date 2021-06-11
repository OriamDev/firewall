<?php

session_start();

require __DIR__ . '/../../vendor/abeautifulsite/simple-php-captcha/simple-php-captcha.php';
require __DIR__ . '/../helpers/helper.php';

    if($_SESSION['captcha_attempts'] > 4)
    {
        updateBlackList();
        unset($_SESSION['captcha']);
        unset($_SESSION['url']);
        die('MAX_CAPTCHA_ATTEMPTS');
    }


    if(isset($_POST['captcha']) && !empty($_POST['captcha']) && strlen($_POST['captcha']) == 5)
    {
        if(strtolower($_POST['captcha']) == strtolower($_SESSION['captcha']['code']))
        {
            $url = "http://" .$_SESSION['url'];

            unset($_SESSION['captcha']);
            unset($_SESSION['url']);
            unset($_SESSION['captcha_attempts']);

            header("Location:" .$url);

            die();
        }
    }

    $_SESSION['captcha'] = simple_php_captcha();
    $_SESSION['captcha_attempts'] += 1;
    require __DIR__ .'/../views/error.phtml';