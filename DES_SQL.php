<?php
// 加密密钥
$key = "1jm4wff";   //PHP只能填充8个字节及以下的key，8个字节以上会报错，而js加密库可以超过这个限制

// 待加密的参数
$param = $_GET['param1'];

// ECB模式不需要初始化向量（IV）
$iv = null;

// 填充方式，每一个传入的参数都需要做处理
$block_size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_ECB);
$pad_size = $block_size - (strlen($param) % $block_size);
$param .= str_repeat(chr($pad_size), $pad_size);

// 加密
$encrypted = mcrypt_encrypt(MCRYPT_DES, $key, $param, MCRYPT_MODE_ECB, $iv);   //ECB模式

// 将加密后的值转为 base64 编码
$encoded = base64_encode($encrypted);

// 创建 cURL 句柄
$curl = curl_init();

// 拼接请求的 URL 和参数
$url = 'http://example.com/api?key=' . urlencode($encoded);
//echo $url;

// 设置请求的 URL
curl_setopt($curl, CURLOPT_URL, $url);

// 设置请求方式为 GET
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   //使用CURLOPT_HTTPGET页面会返回一个1，布尔

// 设置自定义请求头
$headers = array(
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36',
);

curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

// 执行请求并获取响应内容
$response = curl_exec($curl);

// 关闭 cURL 句柄
curl_close($curl);

// 输出响应内容
echo $response;

?>