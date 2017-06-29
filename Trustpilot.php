<?php

/**
 * Class Trustpilot to generate unique links
 */
class Trustpilot
{
    const DEFAULT_BASE_DOMAIN = 'www.trustpilot.com';

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $encryptKey;

    /**
     * @var string
     */
    protected $authKey;

    /**
     * Class constructor
     *
     * @param string $domain     domain name of your project
     * @param string $encryptKey base64 encoded string (optional)
     * @param string $authKey    base64 encoded string (optional)
     */
    public function __construct($domain, $encryptKey = null, $authKey = null)
    {
        $this->domain = $domain;
        if ($encryptKey) {
            $this->setEncryptKey($encryptKey);
        }
        if ($authKey) {
            $this->setAuthKey($authKey);
        }
    }

    /**
     * @param array  $payload    array of your order data
     * @param string $baseDomain base trustpilot domain (optional)
     *
     * @return string
     */
    public function getInvitationLink(array $payload, $baseDomain = self::DEFAULT_BASE_DOMAIN)
    {
        return sprintf(
            'https://%s/evaluate-bgl/%s?p=%s',
            $baseDomain,
            $this->domain,
            $this->encryptPayload($payload)
        );
    }

    /**
     * @param array $payload array of your order data
     *
     * @return string
     */
    public function encryptPayload(array $payload)
    {
        // Generate an Initialization Vector (IV) according to the block size (128 bits)
        $iVector = openssl_random_pseudo_bytes(
            openssl_cipher_iv_length('AES-128-CBC')
        );

        //Encrypting the JSON with the encryptkey and IV with AES-CBC with key size of 256 bits, openssl_encrypt uses PKCS7 padding as default
        $payloadEncrypted = openssl_encrypt(
            json_encode($payload),
            'AES-256-CBC',
            $this->encryptKey,
            OPENSSL_RAW_DATA,
            $iVector
        );

        //Create a signature of the ciphertext.
        $hmac = hash_hmac(
            'sha256',
            $iVector . $payloadEncrypted,
            $this->authKey,
            true
        );

        //Now base64-encode the IV + ciphertext + HMAC:
        $base64Payload = base64_encode($iVector . $payloadEncrypted . $hmac);

        return urlencode($base64Payload);
    }

    /**
     * Setter of EncryptKey
     *
     * @param string $encryptKey
     * @param bool   $encoded
     *
     * @return static
     */
    public function setEncryptKey($encryptKey, $encoded = true)
    {
        $this->encryptKey = $encoded ? base64_decode($encryptKey) : $encryptKey;

        return $this;
    }

    /**
     * Setter of AuthKey
     *
     * @param string $authKey
     * @param bool   $encoded
     *
     * @return static
     */
    public function setAuthKey($authKey, $encoded = true)
    {
        $this->authKey = $encoded ? base64_decode($authKey) : $encoded;

        return $this;
    }
}
