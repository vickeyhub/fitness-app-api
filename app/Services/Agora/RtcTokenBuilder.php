<?php

namespace App\Services\Agora;

use App\Services\Agora\AccessToken2;

class RtcTokenBuilder
{
    const RoleAttendee = 0;
    const RolePublisher = 1;
    const RoleSubscriber = 2;
    const RoleAdmin = 101;

    /**
     * Build the RTC token with uid.
     *
     * @param string $appId The App ID issued to you by Agora.
     * @param string $appCertificate Certificate of the application that you registered in the Agora Console.
     * @param string $channelName Unique channel name for the AgoraRTC session in the string format.
     * @param string $uid User ID. A 32-bit unsigned integer with a value ranging from 1 to (232-1).
     * @param int $role Role of the user.
     * @param int $privilegeExpiredTs Represented by the number of seconds elapsed since 1/1/1970. If, for example, you want to access the Agora Service within 10 minutes after the token is generated, set expireTimestamp as the current timestamp + 600 (seconds).
     * @return string The RTC token.
     */
    public static function buildTokenWithUid($appId, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs)
    {
        return self::buildTokenWithAccount($appId, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
    }

    /**
     * Build the RTC token with account.
     *
     * @param string $appId The App ID issued to you by Agora.
     * @param string $appCertificate Certificate of the application that you registered in the Agora Console.
     * @param string $channelName Unique channel name for the AgoraRTC session in the string format.
     * @param string $account The user's account, max length 255 Bytes.
     * @param int $role Role of the user.
     * @param int $privilegeExpiredTs Represented by the number of seconds elapsed since 1/1/1970. If, for example, you want to access the Agora Service within 10 minutes after the token is generated, set expireTimestamp as the current timestamp + 600 (seconds).
     * @return string The RTC token.
     */
    public static function buildTokenWithAccount($appId, $appCertificate, $channelName, $account, $role, $privilegeExpiredTs)
    {
        $token = AccessToken2::init($appId, $appCertificate, $channelName, $account);
        $Privileges = AccessToken2::Privileges;
        $token->addPrivilege($Privileges["kJoinChannel"], $privilegeExpiredTs);
        if (($role == self::RoleAttendee) ||
            ($role == self::RolePublisher) ||
            ($role == self::RoleAdmin)
        ) {
            $token->addPrivilege($Privileges["kPublishAudioStream"], $privilegeExpiredTs);
            $token->addPrivilege($Privileges["kPublishVideoStream"], $privilegeExpiredTs);
            $token->addPrivilege($Privileges["kPublishDataStream"], $privilegeExpiredTs);
        }
        return $token->build();
    }
}
