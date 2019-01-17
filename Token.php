<?php
declare(strict_types=1);

namespace Webiik\Token;

class Token
{
    /**
     * @param int $strength
     * @return string
     * @throws \Exception
     */
    public function generate($strength = 16): string
    {
        $token = '';

        if (function_exists('random_bytes')) {
            $rawToken = random_bytes($strength);
            if ($rawToken !== false) {
                $token = bin2hex($rawToken);
            }
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $rawToken = openssl_random_pseudo_bytes($strength);
            if ($rawToken !== false) {
                $token = bin2hex($rawToken);
            }
        }

        if (!$token) {
            throw new \Exception('Can\'t generate secure token.');
        }

        return $token;
    }

    /**
     * Generate fast but unsafe token
     * @param int $length
     * @return string
     */
    public function generateCheap($length = 32): string
    {
        $token = '';
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[mt_rand(0, 61)];
        }
        return $token;
    }


    /**
     * Timing-attack safe token comparison (non unicode)
     * @param string $imprint
     * @param string $original
     * @return bool
     */
    public function compare(string $imprint, string $original): bool
    {
        $originalLength = strlen($original);
        $imprintLength = strlen($imprint);

        // Always compare whole length of the original to prevent the timing-attack
        for ($i = 0; $i < $originalLength; $i++) {
            $imprintChar = isset($imprint[$i]) ? $imprint[$i] : '';
            if (ord($original[$i]) !== ord($imprintChar)) {
                $notEqual = true;
            }
        }

        return isset($notEqual) || $originalLength < $imprintLength ? false : true;
    }
}
