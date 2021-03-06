<?php

namespace PaymentSuite\StripeBundle\Services\Interfaces;

use PaymentSuite\PaymentCoreBundle\PaymentMethodInterface;

interface StripeSettingsProviderInterface extends PaymentMethodInterface
{
    /**
     * Gets stripe public key.
     *
     * @return string
     */
    public function getPublicKey(): string;

    /**
     * Gets stripe private key.
     *
     * @return string
     */
    public function getPrivateKey(): string;
}
