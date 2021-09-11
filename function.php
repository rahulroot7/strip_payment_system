<?php
function getTVEmail($tv = null){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://us-central1-belkavpn-develop.cloudfunctions.net/get-user-by-code',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => array('code' => $tv),
	));

	$response = curl_exec($curl);
	curl_close($curl);
	$response = json_decode($response);
	if(!empty($response->email)){

		return $response->email;
	}

	
}

?>