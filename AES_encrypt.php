<!DOCTYPE html>
<html>
<head>
    <title>AES Crypto</title>
</head>
<body>
    <?php
    if(isset($_POST['submit'])) {  // 是否提交了表单
        $key = $_POST['key'];  // 获取加密密钥

        if(isset($_FILES['file'])) {  // 是否选择了文件
            $file = $_FILES['file'];

            if($file['error'] == UPLOAD_ERR_OK) {  // 检查上传的文件是否有错误
                $handle = fopen($file['tmp_name'], "r");

                // Read the file line by line
                while (($line = fgets($handle)) !== false) {  // 逐行读取文件内容
                    //echo "Original: " . $line . "<br>";  // 输出原始内容
                    $lines[] = trim($line);  // 去掉每行内容前后的空格，并保存到数组中
                }
    ?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
                <script>
                    var key = "<?php echo $key ?>";  // 将加密密钥转换成JS变量
                    var keyHex = CryptoJS.enc.Utf8.parse(key);  // 将加密密钥转换成CryptoJS的字节数组格式
                    <?php foreach($lines as $line) { ?> 
                        var encrypted = CryptoJS.AES.encrypt("<?php echo $line ?>", keyHex, { mode: CryptoJS.mode.ECB, padding: CryptoJS.pad.Pkcs7 });
                        document.write("Encrypted: " + encrypted.toString() + " Original: <?php echo $line ?><br>");
                    <?php } ?>               
                </script>
                <?php

                fclose($handle);
            } else {
                echo "Error uploading file: " . $file['error'];
            }
        } else {
            echo "No file uploaded.";
        }
    }
    ?>

    <form action="" method="post" enctype="multipart/form-data">  <!-- 上传表单 -->
        <label for="file">Select a file to upload:</label>
        <input type="file" id="file" name="file"><br>
        <label for="key">Encryption key:</label>
        <input type="text" id="key" name="key"><br>
        <input type="submit" name="submit" value="Upload and Encrypt">
    </form>
</body>
</html>
