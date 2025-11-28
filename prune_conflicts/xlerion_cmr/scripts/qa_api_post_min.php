<?php
$payload = ['name'=>'tmp-api','description'=>'desc','data'=>['html'=>'<p>hi</p>']];
$ch = curl_init('http://127.0.0.1:8080/api/templates');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer devtoken123', 'Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$res = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
echo "HTTP: " . ($info['http_code'] ?? 0) . "\n";
echo $res . "\n";
