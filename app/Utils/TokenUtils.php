<?php
namespace App\Utils;

use Exception;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\Clock\SystemClock;
use DateTimeImmutable;
use DateTimeZone;

trait TokenUtils
{
    public function tokenDecode($token)
    {
        try {
            $now = new DateTimeImmutable('now', new DateTimeZone(getenv('APP_TIMEZONE')));
            $config = Configuration::forSymmetricSigner(new Sha256(), Key\InMemory::plainText(getenv('JWT_SECRET')));
            $tokenParsed = $config->parser()->parse($token);
            $config->validator()->assert($tokenParsed, new LooseValidAt(SystemClock::fromSystemTimezone()));
            $result = [];
            $result['isValid'] = true;
            $claims = $tokenParsed->claims();
            $result['claims'] = [
                'uid' => $claims->get('uid'),
                'location' => $claims->get('location'),
                'location_uuid' => $claims->get('location_uuid'),
                'generated_at' => $claims->get('generated_at'),
            ];
            $result['message'] = 'Token valid';
            
            return (object) $result;
        } catch (RequiredConstraintsViolated $e) {
            $result = [];
            $result['isValid'] = false;
            $result['message'] = $e->getMessage();
            return (object) $result;
        }
    }

    public function readTokenDecode($token)
    {
        try {
            $now = new DateTimeImmutable('now', new DateTimeZone(getenv('APP_TIMEZONE')));
            $config = Configuration::forSymmetricSigner(new Sha256(), Key\InMemory::plainText(getenv('JWT_SECRET')));
            $tokenParsed = $config->parser()->parse($token);
            $config->validator()->assert($tokenParsed, new LooseValidAt(SystemClock::fromSystemTimezone()));
            $result = [];
            $result['isValid'] = true;
            $claims = $tokenParsed->claims();
            $result['claims'] = [
                'uid' => $claims->get('uid'),
                'book_uuid' => $claims->get('book_uuid'),
                'location' => $claims->get('location'),
                'generated_at' => $claims->get('generated_at'),
            ];
            $result['message'] = 'Token valid';
            return (object) $result;
        } catch (RequiredConstraintsViolated $e) {
            $result = [];
            $result['isValid'] = false;
            $result['message'] = $e->getMessage();
            return (object) $result;
        }
    }
}