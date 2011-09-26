<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @Author	Freek Lijten
 */

$requestUrl 	= 'http://oauth.freek/oauth/provider/request_token.php';
$authorizeUrl   = 'http://oauth.freek/oauth/provider/authorize.php';
$callbackUrl    = 'http://oauth.freek/oauth/consumer/get_access_token.php';

$consumerKey 	= 'f06bd5088404d3d1bd1fedbdeb434bb8d28bae6c';
$consumerSecret = '32416bec56b6c962105cdbb99ea190a3067bd3ed';

session_start();
try {
	$OAuth              = new OAuth($consumerKey, $consumerSecret);
	$tokenInfo          = $OAuth->getRequestToken(
		$requestUrl .
		'?oauth_callback=' .
		$callbackUrl .
		'&scope=all'
	);

	echo 'tokeninfo: <pre>';
	var_dump($tokenInfo);
	echo '</pre>';
} catch (Exception $E) {
	echo '<pre>';
	var_dump($E->getMessage());
	var_dump($OAuth->getLastResponse());
	var_dump($OAuth->getLastResponseInfo());
	echo '</pre>';
}


exit;
$_SESSION['oauth_token_secret'] = $tokenInfo['oauth_token_secret'];

$location = $authorizeUrl . '?oauth_token=' . $tokenInfo['oauth_token'];
header('Location: ' . $location);
