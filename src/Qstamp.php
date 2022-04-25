<?php

namespace Alzee\Qstamp;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Dotenv\Dotenv;

class Qstamp
{
    private $uuid = '0X3600303238511239343734';
    private $T_STAMP= 'C4NyFxsNsBuQ5PdsCbaGzYeUQ6u6bT4Teg6BUE1it';
    private $T_FINGERPRINT = '3WK7zYJYf5SyLeiEqedzYYWbwddQMeEi3nwbTujq';
    private $stamp_token;
    private $url;
    private $client;
    private $dotenv;

    public function __construct(HttpClientInterface $client)
    {
        $this->stamp_token = $_ENV['stamp_token'];
        $this->url = $_ENV['api_url'];
        $this->client = $client;
        // $client = HttpClient::create();
        $dotenv = new Dotenv();
        $dotenv->loadEnv(__DIR__.'/.env');
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
        $response = $this->client->request(
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
}
