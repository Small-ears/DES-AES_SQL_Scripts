<!DOCTYPE html>
<html>
<head>
    <title>DES Crypto</title>
</head>
<body>
    <form action="" method="post">  <!-- 解密表单 -->
        <label for="key">Decryption key:</label>
        <input type="text" id="key" name="key"><br>
        <label for="encrypted">Encrypted text:</label>
        <input type="text" id="encrypted" name="encrypted"><br>
        <input type="submit" name="submit" value="Decrypt">
    </form>
    <?php
    if(isset($_POST['submit'])) {  // 是否提交了表单
        $key = $_POST['key'];  // 获取加密密钥
        $encrypted = $_POST['encrypted'];  // 获取密文
    ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
        <script>
            var key = "<?php echo $key; ?>";
            var keyHex = CryptoJS.enc.Utf8.parse(key);
            var encrypted = "<?php echo $encrypted; ?>";
            var decrypted = CryptoJS.DES.decrypt(encrypted, keyHex, { mode: CryptoJS.mode.ECB, padding: CryptoJS.pad.Pkcs7 }).toString(CryptoJS.enc.Utf8);
            document.write("Encrypted: " + encrypted.toString() + "<br>");
            document.write("Decrypted: " + decrypted + "<br>");
        </script>
    <?php
    }
    ?>
</body>
</html>