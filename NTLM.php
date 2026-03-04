<?php

ini_set('display_errors', 'stderr');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

use GuzzleHttp\Client as GuzzleHttp;

// Using Guzzle
// $client = new Client();
$client = new GuzzleHttp();
// $request = new Request('get', 'https://my-exchange-url.com');
$request = $client->request('get', 'http://domen-local.com');

$user_name = 'user_name';
$password = 'password';
$target_name = 'target_name';
$host_name = 'host_name';

$encoding_converter = new MbstringEncodingConverter();
$random_byte_generator = new NativeRandomByteGenerator();
$hasher_factory = HasherFactory::createWithDetectedSupportedAlgorithms();

$negotiate_message_encoder = new NegotiateMessageEncoder($encoding_converter);
$challenge_message_decoder = new ChallengeMessageDecoder();

$keyed_hasher_factory = KeyedHasherFactory::createWithDetectedSupportedAlgorithms();

$nt1_hasher = new NtV1Hasher($hasher_factory, $encoding_converter);
$nt2_hasher = new NtV2Hasher($nt1_hasher, $keyed_hasher_factory, $encoding_converter);

$authenticate_message_encoder = new NtlmV2AuthenticateMessageEncoder(
    $encoding_converter,
    $nt2_hasher,
    $random_byte_generator,
    $keyed_hasher_factory
);

$negotiate_message = $negotiate_message_encoder->encode(
    $target_name,
    $host_name
);

// Send negotiate message
$request->setHeader('Authorization', sprintf('NTLM %s', base64_encode($negotiate_message)));
$response = $client->send($request);

var_dump($response);

// // Decode returned challenge message
// $authenticate_headers = $response->getHeaderAsArray('WWW-Authenticate');
// foreach ($authenticate_headers as $header_string) {
//     $ntlm_matches = preg_match('/NTLM( (.*))?/', $header_string, $ntlm_header);

//     if (0 < $ntlm_matches && isset($ntlm_header[2])) {
//         $raw_server_challenge = base64_decode($ntlm_header[2]);
//         break;
//     }
// }
// $server_challenge = $challenge_message_decoder->decode($raw_server_challenge);

// $authenticate_message = $authenticate_message_encoder->encode(
//     $user_name,
//     $target_name,
//     $host_name,
//     new Password($password),
//     $server_challenge
// );

// // Send authenticate message
// $request->setHeader('Authorization', sprintf('NTLM %s', base64_encode($authenticate_message)));
// $client->send($request);