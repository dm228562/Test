<?php
class Task
{
    private $str;

    public function __construct($str)
    {
        $this->str = $str;
    }

    public function encrypt()
    {
      $plaintext = $this->str;
      $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
      $iv = openssl_random_pseudo_bytes($ivlen);
      $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
      $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
      $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
      return $ciphertext;
    }

    public function decrypt($ciphertext){
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        if (hash_equals($hmac, $calcmac))
        {
            return $original_plaintext;
        }
    }
}

class TaskFactory
{
    public static function create($str)
    {
        return new Task($str);
    }
}
$original = 'Datta Machave';
$v = TaskFactory::create($original);
$enval = $v->encrypt();
echo " <b>Encrypted Value: </b> ".$enval."<br/><br/>";
echo " <b>Decrypted Value: </b> ".$v->decrypt($enval);
