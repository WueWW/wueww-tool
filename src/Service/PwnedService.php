<?php

namespace App\Service;

use GuzzleHttp\Client as Guzzle;

class PwnedService
{
    public function isPwned(string $password)
    {
        $client = new Guzzle();

        $sha1 = \sha1($password);
        $range = \substr($sha1, 0, 5);
        $list = explode("\n", $client->request('GET', 'https://api.pwnedpasswords.com/range/' . $range)->getBody());

        foreach ($list as $line) {
            $hash = strtolower(strtok($line, ':'));

            if ($range . $hash === $sha1) {
                return true;
            }
        }

        return false;
    }
}
