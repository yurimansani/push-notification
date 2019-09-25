<?php

// API access key from Google API's Console
define( 'API_ACCESS_KEY', 'AAAA6CBeGTM:APA91bHubWqkWbVhCSD4xo6QWSFE6DBlErgqPz76o-PgpMNajnH851cSBnXRQ9VBIiEv2XPZwIzd5ZMo_Xd3sRx5XBVp4UBgk5tUF3UJBYnvpeSR6poOf7os8nKor96Dhj9QjBhkne1N');


$registrationId = 'db3W54qi6ew:APA91bGYMEn8LvftKkOaVVfN-HFQ6a3SPGh8tfWU0cKAKeD1_I8Jcqiuojfr_FTeOUptkZriI5p9SQ7v-3i_q1Rp2yv1e1uc0YKpwSUPsrpsX-aRwPP44xPdObJkTXtxcAhtssmsdKc4';

// prep the bundle
$msg = array
(
    'text'    => 'here is a message. message',
    'title'      => 'This is a title. title',
    'sound'     => 'default',
);

$toSend = array
(
    'to' => $registrationId,
    'notification' => $msg,
    'priority' => 'high'
);

$headers = array
(
    'Authorization: key=' . API_ACCESS_KEY,
    'Content-Type: application/json'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
$result = curl_exec($ch);
curl_close($ch);
echo '<pre>';
echo $result;