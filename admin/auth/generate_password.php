<?php
// Password yang ingin kamu hash
$password = 'osissmkn1lmj';

// Buat hash-nya
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Tampilkan hasil hash
echo $hashedPassword;
?>