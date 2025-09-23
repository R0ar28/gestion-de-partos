<?php
namespace App\Service;

class GenerateTrackingCode
{
    private const LENGTH = 10;
    private const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&*?';
    public function generateRandomTrackingCode(int $length = self::LENGTH): string
    {
        $characterLength = strlen(self::CHARACTERS);
        $code = '';

        for ($i = 0; $i < $length; $i++)
        {
            $code .= self::CHARACTERS[random_int(0, $characterLength - 1)];
        }

        return $code;
    }
}