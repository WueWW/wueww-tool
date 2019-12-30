<?php

namespace App\DTO;

class LogoUpload
{
    /**
     * @var string
     */
    private $masterRequestUri;

    /**
     * @return string
     */
    public function getMasterRequestUri(): string
    {
        return $this->masterRequestUri;
    }

    /**
     * @param string $masterRequestUri
     * @return LogoUpload
     */
    public function setMasterRequestUri(string $masterRequestUri): LogoUpload
    {
        $this->masterRequestUri = $masterRequestUri;
        return $this;
    }
}
