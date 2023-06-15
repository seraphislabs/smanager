<?php

class PasswordEncrypt {
    public static function Encrypt($_password) {
        return sodium_crypto_pwhash_str($_password,SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE);
    }
    public static function Check($entered_password, $stored_password) {
        return sodium_crypto_pwhash_str_verify($stored_password, $entered_password);
    }
}

?>