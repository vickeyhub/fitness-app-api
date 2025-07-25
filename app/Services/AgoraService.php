<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
// use Taylanunutmaz\AgoraTokenBuilder\RtmTokenBuilder;
use TaylanUnutmaz\AgoraTokenBuilder\RtmTokenBuilder;

class AgoraService
{
    /**
     * Generate an Agora RTM token for a given agora_uid.
     *
     * @param string $agoraUid
     * @param int $expireInSeconds
     * @return string
     */
    public function generateRtmToken(string $agoraUid, int $expireInSeconds = 3600): string
    {
        $appId = Config::get('agora.app_id');
        $certificate = Config::get('agora.certificate');
        $role = RtmTokenBuilder::RoleRtmUser;
        $privilegeExpiredTs = time() + $expireInSeconds;

        return RtmTokenBuilder::buildToken($appId, $certificate, $agoraUid, $role, $privilegeExpiredTs);
    }

    public function getAppId(): string
    {
        return Config::get('agora.app_id');
    }
}
