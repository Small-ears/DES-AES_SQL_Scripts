<!DOCTYPE html>
<html>
<head>
    <title>DES Crypto</title>
</head>
<body>
    <?php
    // PHP code here
    if(isset($_POST['submit'])) {
        $key = $_POST['key'];

        if(isset($_FILES['file'])) {
            $file = $_FILES['file'];

            if($file['error'] == UPLOAD_ERR_OK) {
                $handle = fopen($file['tmp_name'], "r");

                while (($line = fgets($handle)) !== false) {
                    $lines[] = trim($line);
                }

                // JS code here
                ?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
                <script>
                    var key = "<?php echo $key ?>";
                    var keyHex = CryptoJS.enc.Utf8.parse(key);

                    <?php
                    foreach($lines as $line) {
                        ?>
                        var encrypted = CryptoJS.DES.encrypt("<?php echo $line ?>", keyHex, { mode: CryptoJS.mode.ECB, padding: CryptoJS.pad.Pkcs7 });
                        document.write("Encrypted: " + encrypted.toString() + " Original: <?php echo $line ?><br>");
                        <?php
                    }
                    ?>
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
    <form action="" method="post" enctype="multipart/form-data">
        <label for="file">Select a file to upload:</label>
        <input type="file" id="file" name="file"><br>
        <label for="key">Encryption key:</label>
        <input type="text" id="key" name="key"><br>
        <input type="submit" name="submit" value="Upload and Encrypt">
    </form>
</body>
</html>