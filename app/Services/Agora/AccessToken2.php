<?php

namespace App\Services\Agora;

class AccessToken2
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
        $this->message = new \App\Services\Agora\Message();
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

    /**
     * Enhanced token validation
     */
    public static function validateToken($token, $appID, $appCertificate)
    {
        try {
            // Basic format validation
            if (empty($token) || !preg_match('/^[A-Za-z0-9\-_]+$/', $token)) {
                return false;
            }

            // Decode token
            $data = base64_decode(strtr($token, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($token)) % 4));
            if ($data === false) {
                return false;
            }

            $message = \App\Services\Agora\Message::unpack($data);

            // Validate app ID
            if ($message->appID !== $appID) {
                return false;
            }

            // Validate timestamp (token should not be expired)
            if ($message->ts < time()) {
                return false;
            }

            // Validate signature
            $msg = $message->pack();
            $sig = hash_hmac('sha256', $msg, $appCertificate, true);

            return hash_equals($sig, $message->signature);
        } catch (\Exception $e) {
            return false;
        }
    }
}
