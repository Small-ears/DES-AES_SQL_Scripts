<?php
$key = "akcdefg3bcdkwefs";
$plaintext = "123456";
$plaintext2 = "1234567";

//初始向量IV的生成
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC); // 获取所选模式的IV大小
$iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM); // 生成随机的IV

$block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);  // 加密模式

// 填充方式，每一个传入的参数都需要做处理
$pad_size = $block_size - (strlen($plaintext) % $block_size);   // 填充（plaintext）
$plaintext .= str_repeat(chr($pad_size), $pad_size);

$pad_size = $block_size - (strlen($plaintext2) % $block_size);  // 填充（plaintext2）
$plaintext2 .= str_repeat(chr($pad_size), $pad_size);

// 加密
$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv); // 在此添加IV
$ciphertext2 = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext2, MCRYPT_MODE_CBC, $iv); // 在此添加IV

// 编码为base64
$ciphertext_base64 = base64_encode($iv.$ciphertext); // 在此添加IV
$ciphertext2_base64 = base64_encode($iv.$ciphertext2); // 在此添加IV

echo $ciphertext_base64;
echo $ciphertext2_base64;
?>

<!-- AES支持iv的模式有以下几种：
CBC (Cipher Block Chaining)模式
CFB (Cipher Feedback)模式
OFB (Output Feedback)模式
CTR (Counter)模式 -->


<!-- 使用固定IV的示例 -->
<!-- 
//注意：mcrypt 扩展已经在 PHP 7.2 中被弃用，建议使用其他加密扩展或者使用 PHP 7.1 及以下版本。
$key = "akcdefg3bcdkfg12";
$plaintext3 = "123456";

$iv = "0123456789abcdef"; // 固定IV

$block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC); // 加密模式

//填充方式，每一个传入的参数都需要做处理
$pad_size = $block_size - (strlen($plaintext3) % $block_size); //填充（plaintext）
$plaintext3 .= str_repeat(chr($pad_size), $pad_size);


// 加密
$ciphertext3 = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext3, MCRYPT_MODE_CBC, $iv);

// 编码为base64
$ciphertext3_base64 = base64_encode($ciphertext3);

echo $ciphertext3_base64; 
-->
