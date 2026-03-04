<?php

	ini_set('display_errors', 'stderr');
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
	require_once 'vendor/autoload.php';

    use GuzzleHttp\Client as GuzzleHttp;

    header("Content-type: application/json; charset=utf-8");

	$client_id = 'c0bdb8f233mshb610b15c5e4318dp15011fjsn281043013967';
	
	$login = (isset($_REQUEST['login'])) ? trim($_REQUEST['login']) : 'login';
	$password = (isset($_REQUEST['password'])) ? trim($_REQUEST['password']) : 'password';
	$result = false;
	$status = 0;
	$err = '';
	$secret = false;

	// $curl = curl_init();
	/*if($curl){
		curl_setopt_array($curl, [
			CURLOPT_URL => "https://otp-authenticator.p.rapidapi.com/new_v2/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTPHEADER => [
				"X-RapidAPI-Host: otp-authenticator.p.rapidapi.com",
				"X-RapidAPI-Key: c0bdb8f233mshb610b15c5e4318dp15011fjsn281043013967"
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			echo $response;
		}
	}*/
	try{
		if ( isset($_REQUEST['2FA']) ) {
			// --------- GuzzleHttp --------- //
			$client = new GuzzleHttp();

			$account = (isset($_REQUEST['login'])) ? trim($_REQUEST['login']) : 'UserDtek';

			$request  = $client->request('POST', 'https://otp-authenticator.p.rapidapi.com/new_v2/', [
				'headers' => [
					'X-RapidAPI-Host' => 'otp-authenticator.p.rapidapi.com',
					'X-RapidAPI-Key' => $client_id,
				]
			]);

			$status =  $request->getStatusCode();
			$response = $request->getBody();

			if ($status == 200) {
				// Rewind the body
				$response->seek(0);
				// Read bytes of the body
				$result = $response->read(1024);
				if ($result) {
					$secret = $result;

					$request = $client->request('POST', 'https://otp-authenticator.p.rapidapi.com/enroll/', [
						'form_params' => [
							'secret' => $secret,
							'account' => $account,
							'issuer' => 'HomeCorp'
						],
						'headers' => [
							'X-RapidAPI-Host' => 'otp-authenticator.p.rapidapi.com',
							'X-RapidAPI-Key' => $client_id,
							'content-type' => 'application/x-www-form-urlencoded',
						],
					]);

					$status =  $request->getStatusCode();
					$response = $request->getBody();

					if ($status == 200) {
						$response->seek(0);
						$result = $response->read(1024);
						if ($result) {
							$request = $client->request('GET', $result);
							$status =  $request->getStatusCode();
							$response = $request->getBody();

							if ($status == 200) {
								if ($response) {
					                $savefile = fopen('QR.png', 'w');
					                fwrite($savefile, $response);
					                fclose($savefile);
					                $result = 'QR.png';
					            }
							}
						}
					}
				}
			}

		}elseif( isset($_REQUEST['validate']) ) {
			$secret = trim($_REQUEST['secret']);
			$code = intval($_REQUEST['code']);

			$client = new GuzzleHttp();

			$request = $client->request('POST', 'https://otp-authenticator.p.rapidapi.com/validate/', [
				'form_params' => [
					'secret' => $secret,
					'code' => $code
				],
				'headers' => [
					'X-RapidAPI-Host' => 'otp-authenticator.p.rapidapi.com',
					'X-RapidAPI-Key' => $client_id,
					'content-type' => 'application/x-www-form-urlencoded',
				],
			]);

			// echo $response->getBody();

			$status =  $request->getStatusCode(); // 200
			$response = $request->getBody();
			if ($status == 200) {
				// Rewind the body
				$response->seek(0);
				// Read bytes of the body
				$result = $response->read(1024);
				// var_dump($result);
			}

		}elseif( isset($_REQUEST['new_v2']) ) {
			// --------- GuzzleHttp --------- //
			$client = new GuzzleHttp();

			$request  = $client->request('POST', 'https://otp-authenticator.p.rapidapi.com/new_v2/', [
				'headers' => [
					'X-RapidAPI-Host' => 'otp-authenticator.p.rapidapi.com',
					'X-RapidAPI-Key' => $client_id,
				]
			]);
			// echo $request ->getBody();
			$status =  $request->getStatusCode(); // 200
			$response = $request->getBody();
			if ($status == 200) {
				// Rewind the body
				$response->seek(0);
				// Read bytes of the body
				$result = $response->read(1024);
			}

			// --------- curl_init --------- //

			/*$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_URL => "https://otp-authenticator.p.rapidapi.com/new_v2/",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => [
					"X-RapidAPI-Host: otp-authenticator.p.rapidapi.com",
					"X-RapidAPI-Key: c0bdb8f233mshb610b15c5e4318dp15011fjsn281043013967",
					"content-type: text/html; charset=UTF-8"
					// "Transfer-Encoding: chunked",
					// "Upgrade: h2,h2c",
					// "X-RateLimit-Requests-Limit: 1000",
					// "X-RateLimit-Requests-Remaining: 957",
					// "X-RateLimit-Requests-Reset: 27107",
					// "Server: RapidAPI-1.2.8",
					// "X-RapidAPI-Version: 1.2.8",
					// "X-RapidAPI-Region: AWS - eu-central-1"
				],
			]);

			$result = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);*/

		}elseif( isset($_REQUEST['enroll']) ){
			// --------- curl_init --------- //

			/*$curl = curl_init();

			curl_setopt_array($curl, [
				CURLOPT_URL => "https://otp-authenticator.p.rapidapi.com/enroll/",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => "secret=PS3MCOGLEKX7ZDRD&account=UserDtek&issuer=HomeCorp",
				CURLOPT_HTTPHEADER => [
					"X-RapidAPI-Host: otp-authenticator.p.rapidapi.com",
					"X-RapidAPI-Key: c0bdb8f233mshb610b15c5e4318dp15011fjsn281043013967",
					"content-type: application/x-www-form-urlencoded"
				],
			]);

			$result = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);*/

			// --------- GuzzleHttp --------- //

			$client = new GuzzleHttp();

			$request = $client->request('POST', 'https://otp-authenticator.p.rapidapi.com/enroll/', [
				'form_params' => [
					'secret' => 'GXFK6ZNSOAZRJKVG',
					'account' => 'UserDtek',
					'issuer' => 'HomeCorp'
				],
				'headers' => [
					'X-RapidAPI-Host' => 'otp-authenticator.p.rapidapi.com',
					'X-RapidAPI-Key' => $client_id,
					'content-type' => 'application/x-www-form-urlencoded',
				],
			]);

			// echo $request ->getBody();
			$status =  $request->getStatusCode(); // 200
			$response = $request->getBody();
			if ($status == 200) {
				// Rewind the body
				$response->seek(0);
				// Read bytes of the body
				$result = $response->read(1024);
			}

		}elseif( isset($_REQUEST['getQR']) ){
			$url = "https://prore.ru/qrler.php?size=200x200&data=otpauth%3A%2F%2Ftotp%2FHomeCorp%3AUserDtek%3Fsecret%3DGXFK6ZNSOAZRJKVG%26issuer%3DHomeCorp&ecc=M";
			// --------- file_get_contents --------- //

			// $qr = file_get_contents($url);
            // $savefile = fopen('QR.png', 'w');
            // fwrite($savefile, $qr);
            // fclose($savefile);

			// --------- curl_init --------- //

			/*$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS , 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);

            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($response) {
                $savefile = fopen('QR2.png', 'w');
                fwrite($savefile, $response);
                fclose($savefile);
            }*/

            // --------- GuzzleHttp --------- //

            $client = new GuzzleHttp();

			$request = $client->request('GET', $url);
			$status =  $request->getStatusCode(); // 200
			$response = $request->getBody();
			if ($status == 200) {
				if ($response) {
	                $savefile = fopen('QR.png', 'w');
	                fwrite($savefile, $response);
	                fclose($savefile);
	            }
			}
		}

	}catch(Exception $e){
        $result = $e->getMessage();
        $status = 500;
    }

	echo json_encode(array('success'=>true, 'result'=>$result, 'status'=>$status, 'err'=>$err, 'secret'=>$secret));
	exit;