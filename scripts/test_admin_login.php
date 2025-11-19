<?php
$url = 'http://127.0.0.1:8080/admin/login.php';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['email'=>'admin@xlerion.com','password'=>'ChangeMe123!']);
$res = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
echo 'HTTP: '.$info['http_code']."\n";
if ($res) echo substr($res,0,400)."\n";
