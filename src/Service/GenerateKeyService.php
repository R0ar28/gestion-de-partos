<?php

namespace App\Service;


class GenerateKeyService
{

    public function generate(int $length): string
    {
        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $maxIndex = strlen($alphabet) - 1;
        $id = '';

        for ($i = 0; $i < $length; $i++) {
            $id .= $alphabet[random_int(0, $maxIndex)];
        }

        return $id;
    }
}