<!DOCTYPE html>
<html>
<head>
    <title>RSA encrypt</title>
</head>
<body>
<?php
if (isset($_FILES['file']) && isset($_POST['public_key'])) {
    $public_key = $_POST['public_key'];
    $file = $_FILES['file'];

    if ($file['error'] == UPLOAD_ERR_OK) {
        $handle = fopen($file['tmp_name'], "r");

        // Read the file line by line
        while (($line = fgets($handle)) !== false) {
            $lines[] = trim($line);
        }

        // JS code here
        ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.0.0/jsencrypt.min.js"></script>
        <script>
        var encryptor = new JSEncrypt();
        var publicKey = "<?php echo $public_key; ?>";
        encryptor.setPublicKey(publicKey);
        <?php foreach ($lines as $line): ?>
            var encrypted = encryptor.encrypt("<?php echo $line; ?>");
            document.write("Encrypted: " + encrypted.toString() + " Original: <?php echo $line; ?><br>");
        <?php endforeach; ?>
        </script>
        <?php

        fclose($handle);
    } else {
        echo "Error uploading file: " . $file['error'];
    }
}
?>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="file">Select a file to encrypt:</label>
        <input type="file" id="file" name="file"><br>
        <label for="public_key">Public key:</label>
        <textarea id="public_key" name="public_key"></textarea><br>
        <input type="submit" value="Encrypt">
    </form>
</body>
</html>
