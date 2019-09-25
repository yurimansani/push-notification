<pre>
<?php

	require_once('push-notification.php');
	
	// Message payload
	$msg_payload = array (
		'mtitle' => 'Test push notification title',
		'mdesc' => 'Test push notification body',


        'title' => 'Test push notification title ',
        'message' => 'Test push notification body',
        'subtitle' => 'fdsf',
        'tickerText' => 'sddfs',
        'msgcnt' => 1,
        'vibrate' => 1
	);
	
	// For Android
	// $regId = 'eNRbqsN_T0E:APA91bHahaCE5nkwIcyNOwBGXJ7LrZvMHBnj2qXxx7x974ZXxobJx5BsgnA35Kn-BvFNP8uFZbyJyfiotd0W9Ab_EvvskCOSjGuKybNn7KBetkeCzUvcSMn_Lmep6TXO_jyyr5W1nupx';
	// $regId = 'db3W54qi6ew:APA91bGYMEn8LvftKkOaVVfN-HFQ6a3SPGh8tfWU0cKAKeD1_I8Jcqiuojfr_FTeOUptkZriI5p9SQ7v-3i_q1Rp2yv1e1uc0YKpwSUPsrpsX-aRwPP44xPdObJkTXtxcAhtssmsdKc4';
	$regId = 'ep2GkkKJYCA:APA91bFRRGIdvpeXr5BkC6qtSR4fWK-feaOg4JGea_SCK1x6RAs1FrRRxRX774-czWQYgAB5PqD11d-EIlAuL4lP1sIw4OgqDJeM3-UBNYvfWkEkEgDTxH2c-1QBv1FvR5umh3h9KUaz';
	// For iOS
	$deviceToken = 'FE66489F304DC75B8D6E8200DFF8A456E8DAEACEC428B427E9518741C92C6660';
	// For WP8
	$uri = 'http://s.notify.live.net/u/1/sin/HmQAAAD1XJMXfQ8SR0b580NcxIoD6G7hIYP9oHvjjpMC2etA7U_xy_xtSAh8tWx7Dul2AZlHqoYzsSQ8jQRQ-pQLAtKW/d2luZG93c3Bob25lZGVmYXVsdA/EKTs2gmt5BG_GB8lKdN_Rg/WuhpYBv02fAmB7tjUfF7DG9aUL4';
	
	// Replace the above variable values
	PushNotifications::android_last($msg_payload, $regId);
	// PushNotifications::android($msg_payload, $regId);

	// PushNotifications::WP8($msg_payload, $uri);

	// PushNotifications::iOS($msg_payload, $deviceToken);