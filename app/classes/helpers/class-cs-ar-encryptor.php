<?php

/**
 * The class that encrypts and decrypts strings
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csAREncryptor
{
    /**
     *  Start this class
     *
     */
    public function __construct()
    {
        //nothing yet...
    }

    /**
     * Generates key for the encryption/decryption process and checks to make sure they are the correct lengths
     *
     * @param [int] $length  How long the key should be
     * @param boolean $useCipher  if true use the AES-256-CBC cipher to generate key, if false just use the length to generate the key
     * @return String  The key to use for the encryption
     */
    public function generate_key($length, $useCipher = false)
    {
        $key;
        $isCorrectLength = false;
        do {
            $key = $useCipher ? openssl_random_pseudo_bytes(openssl_cipher_iv_length(csARConfig::ENCRYPT_MEHTOD)) : openssl_random_pseudo_bytes($length);
            $isCorrectLength = strlen($key) === $length ? true : false;
        } while (!$isCorrectLength);
        return $key;
    }

    /**
     * Runs the string provided by the user through the encryption until it is properly secured
     *
     * @param [String] $input_string  The string that should be encrypted
     * @return String  An encrypted version of the string passed in
     */
    public function encrypt($input_string)
    {
        $encrypted = false;
        $encrypted_value = '';
        do {
            $encrypted_value = $this->encrypt_value($input_string);
            $encrypted = $this->decrypt($encrypted_value) === $input_string ? true : false;
        } while (!$encrypted);
        return $encrypted_value;
    }

    /**
     * Encrypts string based on encryption method specified in the config
     *
     * @param [String] $input_string  The string that should be encrypted
     * @return void  An encrypted version of the string passed in
     */
    public function encrypt_value($input_string)
    {
        $iv = $this->generate_key(16, true);
        $encryption_key = $this->generate_key(32);
        $encrypted = openssl_encrypt($input_string, csARConfig::ENCRYPT_MEHTOD, $encryption_key, 0, $iv);
        return $encrypted . ':' . $encryption_key . ':' . $iv;
    }

    /**
     * Decrypts string before being used in the RESTful API
     *
     * @param [String] $encrypted_input_string  The encrypted string that should be decrypted
     * @return String  The decrypted version of the string passed in
     */
    public function decrypt($encrypted_input_string)
    {
        $parts = explode(':', $encrypted_input_string);
        $decrypted_string = '';
        try {
            $decrypted_string = openssl_decrypt($parts[0], csARConfig::ENCRYPT_MEHTOD, $parts[1], 0, $parts[2]);
        } catch (Exception $e) {}
        return $decrypted_string;
    }
}
