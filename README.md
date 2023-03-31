### 功能：
实现DES/AES/RSA批量加密以及AES/DES SQL注入脚本，适用场景渗透测试前端JS加密的时候，需要将目标网站的加密key提取出来，在使用脚本进行测试。

环境需求：使用 PHP 7.1 及以下版本，因此mcrypt 加密扩展已经在 PHP 7.2 中被弃用，加解密脚本因为直接调用远端加密库，因此需要能访问互联网，其他脚本不用。

脚本说明：

AES_decryption.php  #使用ECB，Pkcs7填充模式，如果需要其他模式可以直接在代码更改mode: CryptoJS.mode.ECB, padding: CryptoJS.pad.Pkcs7即可，代码中已添加详细注释；

AES_encrypt.php     #同上

DES                 #同上

RSA                 #不涉及加密模式以及填充方式

如果需要加入随机数/IV可以参考test文件中的脚本对其他脚本进行修改；


SQL（不同场景需要修改小部分代码）

AES_SQL.php         #POST方式提交，使用ECB以及Pkcs7填充方式，ECB模式无法加入随机数，主要为了配合Sqlmap使用；

DES_SQL.php         #GET方式提交，其余和AES的一样，如果遇到POST的提交方式，把AES的提交方式复制替换就行


AES加密解密脚本演示：（DES和AES一样，包括代码相差无几）


![image](https://user-images.githubusercontent.com/56350031/229034928-fc6c4814-8bce-4358-b387-eb54766dd03f.png)


效果：


![image](https://user-images.githubusercontent.com/56350031/229035051-bdeac323-37ff-4cb3-ac29-6ee98a55fe70.png)

SQL注入脚本演示：

脚本执行流程为GET获取请求后，进行加密，加密后再使用curl给目标站点发送请求，获取响应，sqlmap判断是否存在漏洞；
演示只能证明本端发出的数据包无问题
a、确认经过php脚本的代理后sqlmap依然能判断出存在sql漏洞，这里的数据未加密


在目标网站寻找key的方式：
待完善
