<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

/**
 * Where a device may be sent back to with its code: a URL scheme only our own apps open, like the mobile app's
 * devnull://, or a loopback address the app listens on, which any native app can use, such as a desktop app or a CLI
 * (RFC 8252). The loopback address is parsed rather than matched as a prefix, since http://127.0.0.1:80@evil.example
 * starts like one.
 */
final readonly class DeviceRedirectUri implements ValidationRule
{
    /**
     * @param  list<string>  $prefixes
     */
    public function __construct(private array $prefixes)
    {
        //
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || (! Str::startsWith($value, $this->prefixes) && ! $this->isLoopback($value))) {
            $fail('The :attribute field must start with '.implode(', ', $this->prefixes).', or be a loopback URL like http://127.0.0.1:<port>/.');
        }
    }

    private function isLoopback(string $value): bool
    {
        $url = parse_url($value);

        return is_array($url)
            && ($url['scheme'] ?? null) === 'http'
            && in_array($url['host'] ?? null, ['127.0.0.1', '[::1]'], true)
            && isset($url['port'])
            && ! isset($url['user'])
            && ! isset($url['pass'])
            && ! isset($url['fragment']);
    }
}
