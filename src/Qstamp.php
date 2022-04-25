<?php

namespace Alzee\Qstamp;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Dotenv\Dotenv;

class Qstamp
{
    private $UUID;
    private $TOKEN;
    private $API_URL;
    private $httpClient;
    private $dotenv;

    public function __construct($UUID, $TOKEN)
    {
        $dotenv = new Dotenv();
        $dotenv->loadEnv(__DIR__.'/.env');
        $httpClient = HttpClient::create();

        $this->UUID = $UUID;
        $this->TOKEN = $TOKEN;
        $this->API_URL = $_ENV['QSTAMP_API_URL'];

    }

    public function pushApplication($applicationId, $uid, $totalCount = 3, $needCount=0)
    {
        $api = "/application/push";
        $body = [
            'applicationId' => $applicationId,
            'userId' => $uid,
            'totalCount' => $totalCount,
            // 'needCount' => $needCount,
            'uuid' => $this->UUID
        ];
        $response = $this->request($api, $body);
    }

    public function changeMode($mode)
    {
        $api = "/device/model";
        $body = [
            'model' => $mode,
            'uuid' => $this->UUID
        ];
        $response = $this->request($api, $body);
    }

    public function listFingerprints()
    {
        $api = "/finger/list";
        $body = [
            'uuid' => $this->UUID
        ];
        return $this->request($api, $body);
    }

    public function addFingerprint($uid, $username)
    {
        $api = "/finger/add";
        $body = [
            'userId' => $uid,
            'username' => $username,
            'uuid' => $this->UUID
        ];
        $response = $this->request($api, $body);
    }

    public function delFingerprint($uid)
    {
        $api = "/finger/del";
        $body = [
            'userId' => $uid,
            'uuid' => $this->UUID
        ];
        $response = $this->request($api, $body);
    }

    public function idUse($uid, $username)
    {
        $api = "/device/idUse";
        $body = [
            'userId' => $uid,
            'username' => $username,
            'uuid' => $this->UUID
        ];
        $response = $this->request($api, $body);
    }

    public function setSleepTime($min = 30)
    {
        $api="/device/sleep";
        $body = [
            'sleep' => $min,
            'uuid' => $this->UUID
        ];
        $response = $this->request($api, $body);
    }

    public function records()
    {
        $api = "/record/list";
        $body = [
            'uuid' => $this->UUID
        ];
        return $this->request($api, $body);
    }

    public function getUid($applicant = null)
    {
        $resp = $this->listFingerprints();
        $data = json_decode($resp->getContent(), true)['data'];
        // dump($data);
        if (isset($applicant)) {
            $i = array_search($applicant, array_column($data['list'], 'fingerUsername'));
            $uid = $data['list'][$i]['fingerUserId'];
        } else {
            $uid = (int)$data['total'] + 1;
        }
        return $uid;
    }

    public function request($api, $body)
    {
        $headers = ["tToken: $this->TOKEN"];
        $response = $this->httpClient->request(
            'POST',
            $this->API_URL . $api,
            [
                'headers' => $headers,
                'body' => $body
            ]
        );
        $content = $response->getContent();
        return $response;
    }

    public function uploadPic()
    {
    }
}
