<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Written by the consent banner in the browser, so it is not encrypted.
        'pharmasis_consent',
        // A random token checked on the server; read before cookie decryption.
        'pharmasis_pass',
    ];
}
