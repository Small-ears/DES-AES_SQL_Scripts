<?php
//注意：mcrypt 扩展已经在 PHP 7.2 中被弃用，建议使用其他加密扩展或者使用 PHP 7.1 及以下版本。
$key = "akcdefg3bcdkwefs"; //密钥的长度必须是 16、24、或 32 字节，对应的是 AES-128、AES-192 和 AES-256 加密模式;
$plaintext = "123456";


$block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);  // 加密模式

//填充方式，每一个传入的参数都需要做处理
$pad_size = $block_size - (strlen($plaintext) % $block_size);   //填充（plaintext）
$plaintext .= str_repeat(chr($pad_size), $pad_size);

// 加密
$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_ECB);

// 编码为base64
$ciphertext_base64 = base64_encode($ciphertext);

//输出密文
echo $ciphertext_base64;

//解密
$ciphertext_dec = base64_decode($ciphertext_base64);
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
$iv = str_repeat("\0", $iv_size);
$plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_ECB, $iv);

//去除填充
$pad_size = ord($plaintext_dec[strlen($plaintext_dec) - 1]);
$plaintext_dec = substr($plaintext_dec, 0, -$pad_size);

//输出明文
echo $plaintext_dec;
?>
