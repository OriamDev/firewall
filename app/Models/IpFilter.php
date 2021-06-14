<?php


namespace App\Models;


class IpFilter
{

    private string $ip;

    private string $whiteListRangeDir = __DIR__ . '/../files/whitelistRange.txt';

    private string $whiteListDir = __DIR__ . '/../files/whitelist.txt';

    private string $blackListDir = __DIR__ . '/../files/blacklist.txt';


    public function __construct($ip)
    {
        $this->ip = ip2long($ip);

        if(DEBUG)
        {
            r($ip);
            $ip2long = $this->ip;
            r($ip2long);
            r('IpFilter');
        }

    }

    public function check(): bool
    {
        $checkIpInRangeResult = $this->checkIpInRange();

        if(DEBUG)
            r($checkIpInRangeResult);

        if($checkIpInRangeResult)
            return true;

        $checkIpInWhiteList = $this->checkIpInWhiteList();
        if(DEBUG)
            r($checkIpInWhiteList);

        if($checkIpInWhiteList)
            return true;

        $checkIpInBlackList = $this->checkIpInBlackList();
        if(DEBUG)
            r($checkIpInBlackList);

        if($checkIpInBlackList)
        {
            require __DIR__ . '/../views/banned.phtml';
            return false;
        }


        return true;
    }


    private function checkIpInRange(): bool
    {
        if(DEBUG)
            r('checkIpInRange');

        $fileContent = $this->readFile($this->whiteListRangeDir, ';');

        if(DEBUG)
            r(count($fileContent));

        foreach ($fileContent as $range)
        {
            if(strlen($range) > 0)
            {
                $ipRange = explode(',',$range );
                if( $ipRange[0] <= $this->ip && $this->ip<= $ipRange[1])
                    return true;
            }

        }
        return false;
    }


    private function checkIpInWhiteList(): bool
    {
        if(DEBUG)
            r('checkIpInWhiteList');

        $fileContent = $this->readFile($this->whiteListDir);

        if(DEBUG)
            r(count($fileContent));

        return in_array($this->ip, $fileContent);
    }


    private function checkIpInBlackList(): bool
    {
        if(DEBUG)
            r('checkIpInBlackList');

        $fileContent = $this->readFile($this->blackListDir);

        if(DEBUG)
            r(count($fileContent));

        return in_array($this->ip, $fileContent);
    }


    private function readFile($file, $separator = ','): array
    {
        if(filesize($file) == 0)
            return [];

        $fileStream = fopen($file, "r");

        $fileContent = $this->fromFileToArray($separator, $fileStream, $file);

        

        fclose($fileStream);

        return $fileContent;
    }


    private function fromFileToArray($separator, $fileStream, $file): array
    {
        return explode($separator, fread($fileStream, filesize($file)));
    }

}