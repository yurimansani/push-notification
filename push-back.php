public static function android($data, $reg_id) {
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
    		'notification_key' => '996975450419',

            'to' => $reg_id,
    		'senderId' => $reg_id
        	// ]
        ];

        $fields = array(
            'to' => $reg_id,
            'data' => $params, 
        );
// echo "<pre>";
// var_dump(json_encode($params));
// exit();
    	return self::useCurl($url, $headers, json_encode($params));
	}