<?php

/**
 * HELPER FILE.
 * can be used within drupal or standalone.
 */
use Drupal\Core\Site\Settings;

//File accessed via DRUPAL:
if (is_array(Settings::get('webauth_secret_key'))) {
  define('FIRSTKEY', Settings::get('webauth_secret_key')[0]);
  define('SECONDKEY', Settings::get('webauth_secret_key')[1]);
}
//File accessed standalone
else {
  define('FIRSTKEY', $settings['webauth_secret_key'][0]);
  define('SECONDKEY', $settings['webauth_secret_key'][1]);
}

//from https://www.php.net/manual/en/function.openssl-encrypt.php

if(!FIRSTKEY || !SECONDKEY)
{
  throw new Exception('Setting  $settings[\'webauth_secret_key\'] was not found. Please read README');
}

function secured_encrypt($data) {
  $first_key = base64_decode(FIRSTKEY);
  $second_key = base64_decode(SECONDKEY);

  $method = "aes-256-cbc";
  $iv_length = openssl_cipher_iv_length($method);
  $iv = openssl_random_pseudo_bytes($iv_length);

  $first_encrypted = openssl_encrypt($data, $method, $first_key, OPENSSL_RAW_DATA, $iv);
  $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

  $output = base64_encode($iv . $second_encrypted . $first_encrypted);
  return $output;
}

function secured_decrypt($input) {

  $first_key = base64_decode(FIRSTKEY);
  $second_key = base64_decode(SECONDKEY);
  $mix = base64_decode($input);

  $method = "aes-256-cbc";
  $iv_length = openssl_cipher_iv_length($method);

  $iv = substr($mix, 0, $iv_length);
  $second_encrypted = substr($mix, $iv_length, 64);
  $first_encrypted = substr($mix, $iv_length + 64);

  $data = openssl_decrypt($first_encrypted, $method, $first_key, OPENSSL_RAW_DATA, $iv);
  $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

  if (hash_equals($second_encrypted, $second_encrypted_new))
    return $data;

  return false;
}

?>