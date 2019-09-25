<?php 
// Server file
class PushNotifications {
	// (Android)API access key from Google API's Console.
	// private static $API_ACCESS_KEY = 'AAAA6CBeGTM:APA91bHubWqkWbVhCSD4xo6QWSFE6DBlErgqPz76o-PgpMNajnH851cSBnXRQ9VBIiEv2XPZwIzd5ZMo_Xd3sRx5XBVp4UBgk5tUF3UJBYnvpeSR6poOf7os8nKor96Dhj9QjBhkne1N';
	private static $API_ACCESS_KEY = 'AIzaSyAY8O65Jpn2wIAFi_BA2t6jWQ47a77nnmc';
	// (iOS) Private key's passphrase.
	private static $passphrase = 'joashp';
	// (Windows Phone 8) The name of our push channel.
        private static $channelName = "joashp";
	
	// Change the above three vriables as per your app.
	public function __construct() {
		exit('Init function is not allowed');
	}
	
        // Sends Push notification for Android users
	public static function android($data, $reg_id) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $message = array(
            'title' => $data['mtitle'],
            'message' => $data['mdesc'],
            'subtitle' => '',
            'tickerText' => '',
            'msgcnt' => 1,
            'vibrate' => 1
        );
        
        $headers = array(
        	'Authorization: key=' .self::$API_ACCESS_KEY,
        	'Content-Type: application/json'
        );

        $fields = array(
            'registration_ids' => array($reg_id),
            'data' => $message, 
        );

var_dump(json_encode($fields));
// exit();

    	return self::useCurl($url, $headers, json_encode($fields));
	}

	public static function android_last($data, $reg_id) {
		//testefutebolcard
        // $url = 'https://fcm.googleapis.com/v1/projects/testefutebolcard/messages:send';
        $url = 'https://fcm.googleapis.com/fcm/send';
        $message = array(
            'title' => $data['mtitle'],
            'message' => $data['mdesc'],
            'subtitle' => 'fdsf',
            'tickerText' => 'sddfs',
            'msgcnt' => 1,
            'vibrate' => 1
        );
        
        $headers = array(
        	'Authorization: key=' .self::$API_ACCESS_KEY,
        	'Content-Type: application/json',
		);
		var_dump($headers);

        $params = [
        	// 'message' =>[
    			// 'token' => 'AIzaSyAY8O65Jpn2wIAFi_BA2t6jWQ47a77nnmc',
        	// 'content_available' => true,
    		'message' => [
				// 'token' => '7d5f8eff4f775944',
    			'body' => $data['mdesc'],
    			'title' => $data['mtitle'],
    		],
    		'priority' => 'high',
    		// 'registration_ids' => [$reg_id], 
    		// 'notification_key' => '996975450419',

            'to' => $reg_id,
        	// ]
        ];

	var_dump($params);
        $fields = array(
            'to' => $reg_id,
            'data' => $params, 
        );
// echo "<pre>";
// var_dump(json_encode($params));
// exit();
    	return self::useCurl($url, $headers, json_encode($params));
	}
	
	// Sends Push's toast notification for Windows Phone 8 users
	public function WP($data, $uri) {
		$delay = 2;
		$msg =  "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
		        "<wp:Notification xmlns:wp=\"WPNotification\">" .
		            "<wp:Toast>" .
		                "<wp:Text1>".htmlspecialchars($data['mtitle'])."</wp:Text1>" .
		                "<wp:Text2>".htmlspecialchars($data['mdesc'])."</wp:Text2>" .
		            "</wp:Toast>" .
		        "</wp:Notification>";
		
		$sendedheaders =  array(
		    'Content-Type: text/xml',
		    'Accept: application/*',
		    'X-WindowsPhone-Target: toast',
		    "X-NotificationClass: $delay"
		);
		
		$response = $this->useCurl($uri, $sendedheaders, $msg);
		
		$result = array();
		foreach(explode("\n", $response) as $line) {
		    $tab = explode(":", $line, 2);
		    if (count($tab) == 2)
		        $result[$tab[0]] = trim($tab[1]);
		}
		
		return $result;
	}
	
        // Sends Push notification for iOS users
	public function iOS($data, $devicetoken) {
		$deviceToken = $devicetoken;
		$ctx = stream_context_create();
		// ck.pem is your certificate file
		stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', self::$passphrase);
		// Open a connection to the APNS server
		$fp = stream_socket_client(
			'ssl://gateway.sandbox.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);
		// Create the payload body
		$body['aps'] = array(
			'alert' => array(
			    'title' => $data['mtitle'],
                'body' => $data['mdesc'],
			 ),
			'sound' => 'default'
		);
		// Encode the payload as JSON
		$payload = json_encode($body);
		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));
		
		// Close the connection to the server
		fclose($fp);
		if (!$result)
			return 'Message not delivered' . PHP_EOL;
		else
			return 'Message successfully delivered' . PHP_EOL;
	}
	
	// Curl 
	private static function useCurl($url, $headers, $fields = null) {
	        // Open connection
	        $ch = curl_init();
	        if ($url) {
	            // Set the url, number of POST vars, POST data
	            curl_setopt($ch, CURLOPT_URL, $url);
	            // var_dump($url);
	            // exit('fksdljgklg');
	            curl_setopt($ch, CURLOPT_POST, true);
	            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	     
	            // Disabling SSL Certificate support temporarly
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	            if ($fields) {
	                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	            }
	     
	            // Execute post
	            $result = curl_exec($ch);
 				$info = curl_getinfo($ch);
	            var_dump($result);
	            var_dump($info);

	            if ($result === FALSE) {
	                die('Curl failed: ' . curl_error($ch));
	            }
	     
	            // Close connection
	            curl_close($ch);
	
	            return $result;
        }
    }


	public static function enviar_push_android($data, $push_id) {
    $app_key = "";
        $url = 'https://android.googleapis.com/gcm/send';

        $rows[] = [
        	'push_id' => $push_id
        ];

        $ids = "[";
        foreach ($rows as $k => $v) {
            $ids .= '"'. $v["push_id"] . '",';
        }
        $ids = trim($ids, ",") . "]";

        $body = '{"registration_ids" : ' .  $ids . ', "data" : '. json_encode($data) . '}';

        $hdr = '';
        foreach (array(
                     "Content-Type" => "application/json",
                     "Authorization" => "key=". self::$API_ACCESS_KEY,
                 ) as $k => $v) {
            $hdr .= $k . ":" . $v . "\r\n";
        }
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => $hdr,
                'content' => $body,
                'timeout' => 60,
                'ignore_errors' => true
            )
        );
        echo "<pre>";
        print_r($opts);exit();
        $ctx  = stream_context_create($opts);
        $result = file_get_contents($url, false, $ctx , -1, 40000);

        $json2array = json_decode($result, true);

        //comentar print
        //print_pre($json2array);

        $del_types = array('InvalidRegistration','NotRegistered');
        $bad_ids = array();
        $results = $json2array['results'];
        $len = count($results);

        for ($i = 0; $i < $len; $i++) {
            if (!array_key_exists('error', $results[$i]) || !in_array($results[$i]['error'], $del_types))
                continue;

            if(array_key_exists($i ,$rows))
                $bad_ids[] = $rows[$i]['id_device'];
        }//for

        $bad_ids = array_filter($bad_ids);

        $count_bi = count($bad_ids);

        if ($count_bi == 0)
            return true;

        //comentar print
        //print_pre("Count do Bad Ids: ".$count_bi);


        //ob_end_flush();
        flush();
        //fastcgi_finish_request(); //cancela execução

        return true;
    }
    
}