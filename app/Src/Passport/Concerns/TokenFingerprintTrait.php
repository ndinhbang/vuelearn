<?php

namespace App\Src\Passport\Concerns;

trait TokenFingerprintTrait
{
    private ?string $fingerprint;

    /**
     * @return string|null
     */
    public function getFingerprint(): string|null
    {
        return $this->fingerprint;
    }

    /**
     * @param string $fingerprint
     */
    public function setFingerprint(string $fingerprint): void
    {
        $this->fingerprint = $fingerprint;
    }
}
