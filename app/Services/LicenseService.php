<?php

namespace App\Services;

class LicenseService
{
    public const ERROR_DOMAIN_MISMATCH = 'domain_mismatch';

    public function __construct()
    {
       
    }

    public function isLocalDomain($domain)
    {
        $localDomains = [
            'localhost',
            '127.0.0.1',
            '::1',
        ];

       
        return in_array($domain, $localDomains) || str_ends_with($domain, '.test');
    }

    public function validate($purchaseCode = null)
    {
       
        return [
            'valid' => true,
            'message' => 'Bypassed license check',
        ];
    }
}