<?php

namespace App\Services\Agora;

class AccessToken
{
    public $appID;
    public $appCertificate;
    public $channelName;
    public $uid;
    public $message;
    public $privileges;

    const Privileges = array(
        "kJoinChannel" => 1,
        "kPublishAudioStream" => 2,
        "kPublishVideoStream" => 3,
        "kPublishDataStream" => 4,
        "kPublishAudioCdn" => 5,
        "kPublishVideoCdn" => 6,
        "kRequestPublishAudioStream" => 7,
        "kRequestPublishVideoStream" => 8,
        "kRequestPublishDataStream" => 9,
        "kInvitePublishAudioStream" => 10,
        "kInvitePublishVideoStream" => 11,
        "kInvitePublishDataStream" => 12,
        "kAdministrateChannel" => 101,
        "kRtmLogin" => 1000,
    );

    public function __construct()
    {
        $this->message = new Message();
    }

    public static function init($appID, $appCertificate, $channelName, $uid)
    {
        $accessToken = new self();
        $accessToken->appID = $appID;
        $accessToken->appCertificate = $appCertificate;
        $accessToken->channelName = $channelName;
        $accessToken->uid = $uid;
        $accessToken->privileges = array();
        return $accessToken;
    }

    public function addPrivilege($privilege, $expireTimestamp)
    {
        $this->privileges[$privilege] = $expireTimestamp;
    }

    public function build()
    {
        $this->message->salt = rand(1, 99999999);
        $this->message->ts = time();
        $this->message->privileges = $this->privileges;
        $this->message->uid = $this->uid;
        $this->message->appID = $this->appID;
        $this->message->channelName = $this->channelName;

        $msg = $this->message->pack();
        $val = array_merge(unpack("C*", $this->appCertificate), unpack("C*", $msg));
        $sig = hash_hmac('sha256', $msg, $this->appCertificate, true);

        $this->message->signature = $sig;
        $data = $this->message->pack();
        return $this->base64EncodeUrl($data);
    }

    public function base64EncodeUrl($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

class Message
{
    public $salt;
    public $ts;
    public $privileges;
    public $uid;
    public $appID;
    public $channelName;
    public $signature;

    public function pack()
    {
        $binary = pack("V", $this->salt);
        $binary .= pack("V", $this->ts);
        $binary .= pack("V", count($this->privileges));
        foreach ($this->privileges as $key => $value) {
            $binary .= pack("V", $key);
            $binary .= pack("V", $value);
        }
        $binary .= pack("V", strlen($this->uid));
        $binary .= $this->uid;
        $binary .= pack("V", strlen($this->appID));
        $binary .= $this->appID;
        $binary .= pack("V", strlen($this->channelName));
        $binary .= $this->channelName;
        $binary .= pack("V", strlen($this->signature));
        $binary .= $this->signature;
        return $binary;
    }
} 