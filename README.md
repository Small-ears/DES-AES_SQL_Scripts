实现DES/AES/RSA批量加密以及AES/DES SQL注入脚本，适用场景渗透测试前端JS加密的时候，需要将目标网站的加密key提取出来，在使用脚本进行测试。

环境需求：使用 PHP 7.1 及以下版本，因此mcrypt 加密扩展已经在 PHP 7.2 中被弃用，加解密脚本因为直接调用远端加密库，因此需要能访问互联网，其他脚本不用。

加密解密脚本演示：
![image](https://user-images.githubusercontent.com/56350031/229034928-fc6c4814-8bce-4358-b387-eb54766dd03f.png)
效果：
![image](https://user-images.githubusercontent.com/56350031/229035051-bdeac323-37ff-4cb3-ac29-6ee98a55fe70.png)
