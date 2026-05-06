<?php
    define("CIPHER_KEY", "Mw5I2r3rVKLWp2ka");

    function encryptData($data) {
        $cipher = "AES-128-CTR"; 
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($data, $cipher, CIPHER_KEY, 0, $iv);
        return base64_encode($iv . $encrypted); 
    }
    
    function decryptData($data) {
        $cipher = "AES-128-CTR";
        $data = base64_decode($data);
        $iv_length = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $iv_length);
        $encrypted = substr($data, $iv_length);
        return openssl_decrypt($encrypted, $cipher, CIPHER_KEY, 0, $iv);
    }
?>