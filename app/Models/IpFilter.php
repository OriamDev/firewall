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
    }

    public function check(): bool
    {
        if($this->checkIpInRange())
            return true;

        if($this->checkIpInWhiteList())
            return true;

        if($this->checkIpInBlackList())
            return false;

        return true;
    }


    private function checkIpInRange(): bool
    {
        foreach ($this->readFile($this->whiteListRangeDir, ';') as $range)
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
        return in_array($this->ip, $this->readFile($this->whiteListDir));
    }


    private function checkIpInBlackList(): bool
    {
        return in_array($this->ip, $this->readFile($this->blackListDir));
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