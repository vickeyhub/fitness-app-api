<?php

namespace App\Services\Agora;

use App\Services\Agora\AccessToken2;

class RtmTokenBuilder
{
    const RoleRtmUser = 1;

    /**
     * Build the RTM token.
     *
     * @param string $appId The App ID issued to you by Agora.
     * @param string $appCertificate Certificate of the application that you registered in the Agora Console.
     * @param string $userId User ID. A 32-bit unsigned integer with a value ranging from 1 to (232-1).
     * @param int $role Role of the user.
     * @param int $privilegeExpiredTs Represented by the number of seconds elapsed since 1/1/1970. If, for example, you want to access the Agora Service within 10 minutes after the token is generated, set expireTimestamp as the current timestamp + 600 (seconds).
     * @return string The RTM token.
     */
    public static function buildToken($appId, $appCertificate, $userId, $role, $privilegeExpiredTs)
    {
        $token = AccessToken2::init($appId, $appCertificate, '', $userId);
        $Privileges = AccessToken2::Privileges;
        $token->addPrivilege($Privileges["kRtmLogin"], $privilegeExpiredTs);
        return $token->build();
    }
}
