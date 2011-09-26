<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @Author	Freek Lijten
 */

$requestUrl 	= 'http://oauth.freek/oauth/provider/request_token.php';
$authorizeUrl   = 'http://oauth.freek/oauth/provider/authorize.php';
$callbackUrl    = 'http://oauth.freek/oauth/consumer/get_access_token.php';

$consumerKey 	= 'b26594220a7728ad931bce3232ee22d2d2520ec6';
$consumerSecret = 'c21528fbf82c209045e85877636beddeb1ab4e48';

session_start();
try {
	$OAuth              = new OAuth($consumerKey, $consumerSecret);
	$tokenInfo          = $OAuth->getRequestToken(
		$requestUrl .
		'?oauth_callback=' .
		$callbackUrl .
		'&scope=all'
	);
} catch (Exception $E) {
	echo '<pre>';
	var_dump($E->getMessage());
	var_dump($OAuth->getLastResponse());
	var_dump($OAuth->getLastResponseInfo());
	echo '</pre>';
}

if (empty($tokenInfo['oauth_token_secret']) || empty($tokenInfo['oauth_token'])) {
	echo '<pre>';
	var_dump($tokenInfo);
	echo '</pre>';

	exit;
}
$_SESSION['oauth_token_secret'] = $tokenInfo['oauth_token_secret'];

$location = $authorizeUrl . '?oauth_token=' . $tokenInfo['oauth_token'];
header('Location: ' . $location);
