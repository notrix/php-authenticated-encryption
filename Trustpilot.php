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
     * @var string
     */
    protected $secretKey;

    /**
     * Class constructor
     *
     * @param string $domain     domain name of your project
     * @param string $encryptKey base64 encoded string (optional)
     * @param string $authKey    base64 encoded string (optional)
     * @param string $secretKey  unique link secret key (optional)
     */
    public function __construct($domain, $encryptKey = null, $authKey = null, $secretKey = null)
    {
        $this->domain = $domain;
        if ($encryptKey) {
            $this->setEncryptKey($encryptKey);
        }
        if ($authKey) {
            $this->setAuthKey($authKey);
        }
        if ($secretKey) {
            $this->setSecretKey($secretKey);
        }
    }

    /**
     * Get invitation link for sharing with your customers
     *
     * @param array  $payload    array of your order data
     * @param string $baseDomain base trustpilot domain (optional)
     *
     * @return string
     */
    public function getInvitationLink(array $payload, $baseDomain = self::DEFAULT_BASE_DOMAIN)
    {
        if (!$this->domain) {
            throw new \InvalidArgumentException('Parameter domain is required');
        }

        return sprintf(
            'https://%s/evaluate-bgl/%s?p=%s',
            $baseDomain,
            $this->domain,
            $this->encryptPayload($payload)
        );
    }

    /**
     * Get unique link for collecting reviews in iframe form
     *
     * @param string $reference  unique reference number of customers order
     * @param string $email      email of your customer
     * @param string $name       name of your customer
     * @param string $baseDomain base trustpilot domain (optional)
     *
     * @return string
     */
    public function getUniqueLink($reference, $email, $name, $baseDomain = self::DEFAULT_BASE_DOMAIN)
    {
        if (!$this->domain || !$this->secretKey) {
            throw new \InvalidArgumentException('Parameters domain and secretKey are required');
        }

        return sprintf(
            'https://%s/evaluate/%s?a=%s&b=%s&c=%s&e=%s',
            $baseDomain,
            $this->domain,
            $reference,
            base64_encode($email),
            urlencode($name),
            hash('sha1', $this->secretKey . $email . $reference)
        );
    }

    /**
     * Get encrypted payload code
     *
     * @param array $payload array of your order data
     *
     * @return string
     */
    public function encryptPayload(array $payload)
    {
        if (!$this->encryptKey || !$this->authKey) {
            throw new \InvalidArgumentException('Parameters encryptKey and authKey are required');
        }

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

    /**
     * Setter of SecretKey
     *
     * @param string $secretKey
     *
     * @return static
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;

        return $this;
    }
}
