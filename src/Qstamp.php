<?php

namespace Alzee\Qstamp;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Dotenv\Dotenv;

class Qstamp
{
    private $uuid = '0X3600303238511239343734';
    private $stamp_token;
    private $url;
    private $httpClient;
    private $dotenv;

    public function __construct(HttpClientInterface $httpClient)
    {
        $dotenv = new Dotenv();
        $dotenv->loadEnv(__DIR__.'/.env');
        $this->httpClient = $httpClient;
        // $httpClient = HttpClient::create();

        $this->stamp_token = $_ENV['stamp_token'];
        $this->url = $_ENV['api_url'];

    }

    public function pushApplication($applicationId, $uid, $totalCount = 3, $needCount=0)
    {
        $api = "/application/push";
        $body = [
            'applicationId' => $applicationId,
            'userId' => $uid,
            'totalCount' => $totalCount,
            // 'needCount' => $needCount,
            'uuid' => $this->uuid
        ];
        $response = $this->request($api, $body);
    }

    public function changeMode($mode)
    {
        $api = "/device/model";
        $body = [
            'model' => $mode,
            'uuid' => $this->uuid
        ];
        $response = $this->request($api, $body);
    }

    public function listFingerprints()
    {
        $api = "/finger/list";
        $body = [
            'uuid' => $this->uuid
        ];
        return $this->request($api, $body);
    }

    public function addFingerprint($uid, $username)
    {
        $api = "/finger/add";
        $body = [
            'userId' => $uid,
            'username' => $username,
            'uuid' => $this->uuid
        ];
        $response = $this->request($api, $body);
    }

    public function delFingerprint($uid)
    {
        $api = "/finger/del";
        $body = [
            'userId' => $uid,
            'uuid' => $this->uuid
        ];
        $response = $this->request($api, $body);
    }

    public function idUse($uid, $username)
    {
        $api = "/device/idUse";
        $body = [
            'userId' => $uid,
            'username' => $username,
            'uuid' => $this->uuid
        ];
        $response = $this->request($api, $body);
    }

    public function setSleepTime($min = 30)
    {
        $api="/device/sleep";
        $body = [
            'sleep' => $min,
            'uuid' => $this->uuid
        ];
        $response = $this->request($api, $body);
    }

    public function records()
    {
        $api = "/record/list";
        $body = [
            'uuid' => $this->uuid
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
        $headers = ["tToken: $this->stamp_token"];
        $response = $this->httpClient->request(
            'POST',
            $this->url . $api,
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
