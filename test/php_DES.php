
<!--
// 加密密钥
$key = "mykey";

// 待加密的参数
$param = "hello world";

// 加密,ECB模式下IV不会被使用
$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB), MCRYPT_RAND);
$encrypted = mcrypt_encrypt(MCRYPT_DES, $key, $param, MCRYPT_MODE_ECB, $iv);

// 将加密后的值转为 base64 编码
$encoded = base64_encode($encrypted);

echo "Encoded value: ".$encoded."<br>";

// 解密
$decoded = base64_decode($encoded);
$decrypted = mcrypt_decrypt(MCRYPT_DES, $key, $decoded, MCRYPT_MODE_ECB, $iv);

echo "Decrypted value: ".$decrypted."<br>"; 
-->


<!-- 
mcrypt_create_iv是用于创建指定长度的随机初始化向量（IV）的函数。该函数有两个参数，第一个参数指定了所需的IV长度，第二个参数指定了用于生成IV的随机数生成器类型。
mcrypt_get_iv_size用于获取指定算法和模式的加密算法所需的IV大小。该函数有两个参数，第一个参数指定了加密算法，第二个参数指定了加密模式。
-->

<!-- 使用IV的CBC模式 -->
<?php
// 加密密钥
$key = "mykey";

// 待加密的参数
$param = "hello world";

// CBC模式需要一个初始化向量（IV），使用随机生成的IV
$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_CBC), MCRYPT_RAND);

// 填充方式，每一个传入的参数都需要做处理
$block_size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
$pad_size = $block_size - (strlen($param) % $block_size);
$param .= str_repeat(chr($pad_size), $pad_size);

// 加密
$encrypted = mcrypt_encrypt(MCRYPT_DES, $key, $param, MCRYPT_MODE_CBC, $iv);

// 将加密后的值转为 base64 编码
$encoded = base64_encode($iv . $encrypted);

echo "Encoded value: ".$encoded."<br>";

// 解密
$decoded = base64_decode($encoded);
$iv_dec = substr($decoded, 0, mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_CBC));
$decoded = substr($decoded, mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_CBC));
$decrypted = mcrypt_decrypt(MCRYPT_DES, $key, $decoded, MCRYPT_MODE_CBC, $iv_dec);

// 移除填充
$pad_char = ord($decrypted[strlen($decrypted)-1]);
$decrypted = substr($decrypted, 0, -$pad_char);

echo "Decrypted value: ".$decrypted."<br>";
?>
