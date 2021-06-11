<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';


/*
$array = [];
for($i = 0; $i < 256; $i++)
{
    $fo = rand(10, 225);
    $so = rand(1, 255);

    $start = "{$fo}.{$so}.{$i}.0";
    $end = "{$fo}.{$so}.255.255";

    array_push($array, [ip2long($start), ip2long($end)]);
}


foreach($array as $value)
{
    $string = "{$value[0]},{$value[1]}";
    file_put_contents(__DIR__ . '/files/whitelistRange.txt', $string . ';', FILE_APPEND | LOCK_EX);
}

die();

/*

$array = [];
for($i = 0; $i < 256; $i++)
{
    for ($j = 0; $j < 256; $j++)
    {
        $fo = rand(10, 225);
        $ip = "{$fo}.22.{$i}.{$j}";
        array_push($array, ip2long($ip));
    }
}

foreach($array as $value)
{
    file_put_contents(__DIR__ . '/files/blacklist.txt', $value . ',', FILE_APPEND | LOCK_EX);
}


die();
/*

$registeredApplications = [
    'firewall_60992bbd0c8f92.22313817'
];

if(!isset($token))
    throw new Exception("Application token is missing");

if(!in_array($token, $registeredApplications))
    throw new Exception("This token not math with any registered application");


*/



(new \App\Models\Firewall());


//ricpcacao@gmail.com