<?php

namespace App\Services\Agora;

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

    public static function unpack($data)
    {
        try {
            $message = new self();
            $offset = 0;
            $dataLength = strlen($data);

            // Check minimum data length
            if ($dataLength < 16) {
                throw new \Exception('Invalid token data length');
            }

            // Unpack salt
            if ($offset + 4 > $dataLength) {
                throw new \Exception('Invalid salt data');
            }
            $message->salt = unpack("V", substr($data, $offset, 4))[1];
            $offset += 4;

            // Unpack timestamp
            if ($offset + 4 > $dataLength) {
                throw new \Exception('Invalid timestamp data');
            }
            $message->ts = unpack("V", substr($data, $offset, 4))[1];
            $offset += 4;

            // Unpack privileges count
            if ($offset + 4 > $dataLength) {
                throw new \Exception('Invalid privileges count');
            }
            $privilegesCount = unpack("V", substr($data, $offset, 4))[1];
            $offset += 4;

            // Unpack privileges
            $message->privileges = array();
            for ($i = 0; $i < $privilegesCount; $i++) {
                if ($offset + 8 > $dataLength) {
                    throw new \Exception('Invalid privileges data');
                }
                $key = unpack("V", substr($data, $offset, 4))[1];
                $offset += 4;
                $value = unpack("V", substr($data, $offset, 4))[1];
                $offset += 4;
                $message->privileges[$key] = $value;
            }

            // Unpack uid
            if ($offset + 4 > $dataLength) {
                throw new \Exception('Invalid uid length');
            }
            $uidLength = unpack("V", substr($data, $offset, 4))[1];
            $offset += 4;
            if ($offset + $uidLength > $dataLength) {
                throw new \Exception('Invalid uid data');
            }
            $message->uid = substr($data, $offset, $uidLength);
            $offset += $uidLength;

            // Unpack appID
            if ($offset + 4 > $dataLength) {
                throw new \Exception('Invalid appID length');
            }
            $appIDLength = unpack("V", substr($data, $offset, 4))[1];
            $offset += 4;
            if ($offset + $appIDLength > $dataLength) {
                throw new \Exception('Invalid appID data');
            }
            $message->appID = substr($data, $offset, $appIDLength);
            $offset += $appIDLength;

            // Unpack channelName
            if ($offset + 4 > $dataLength) {
                throw new \Exception('Invalid channelName length');
            }
            $channelNameLength = unpack("V", substr($data, $offset, 4))[1];
            $offset += 4;
            if ($offset + $channelNameLength > $dataLength) {
                throw new \Exception('Invalid channelName data');
            }
            $message->channelName = substr($data, $offset, $channelNameLength);
            $offset += $channelNameLength;

            // Unpack signature
            if ($offset + 4 > $dataLength) {
                throw new \Exception('Invalid signature length');
            }
            $signatureLength = unpack("V", substr($data, $offset, 4))[1];
            $offset += 4;
            if ($offset + $signatureLength > $dataLength) {
                throw new \Exception('Invalid signature data');
            }
            $message->signature = substr($data, $offset, $signatureLength);

            return $message;
        } catch (\Exception $e) {
            throw new \Exception('Token unpack failed: ' . $e->getMessage());
        }
    }
}
