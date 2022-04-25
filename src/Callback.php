<?php

namespace Alzee\Qstamp;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Alzee\Qstamp\Qstamp;

class Callback
{
    public function qstamp(Request $request): Response
    {
        $data = $request->getContent();
        $data = stripcslashes($data);
        $data = stripcslashes($data);
        $data = str_replace('"{', '{', $data);
        $data = str_replace('}"', '}', $data);
        $data = json_decode($data);
        $uuid = $data->uuid;
        dump($data);
        $msg = match ($data->cmd) {
            1000 => $this->setSleepTime($data->data->sleepTime),
            1130 => $this->uploadPic(),
            default => true,
        };
        $resp = new Response();
        return $resp;
    }
}
