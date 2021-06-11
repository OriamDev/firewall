<?php


namespace App\Models;


class UserAgentFilter
{
    protected string $exclusions;

    private string $ip;

    private string $userAgent;

    public function __construct($ip)
    {
        $this->ip = $ip;
        $this->userAgent = $this->getUserAgent();
        $this->exclusions = $this->compileRegex((new UserAgentExclusions())->getData());
    }

    public function check(): bool
    {
        if($this->checkIfBootBelongsToGoogle())
            return true; // Google Bot

        if($this->userAgentIsInExclusions())
            return true; // Exclusions

        return false; // Trash
    }

    private function compileRegex($patterns): string
    {
        return '('.implode('|', $patterns).')';
    }

    private function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    private function userAgentIsInExclusions(): bool
    {
        if(trim(preg_replace("/{$this->exclusions}/i", '', $this->userAgent)) === '')
            return true;

        return true;
    }

    private function validateGoogleBotIP()
    {
        return preg_match('/\.googlebot\.com$/i', gethostbyaddr($this->ip));
    }

    private function checkIfBootBelongsToGoogle(): bool
    {
        if (strpos($this->userAgent, 'Google') !== false) {

            if ($this->validateGoogleBotIP())
                return true;
             else
                return false;

        } else
            return false;
    }
}