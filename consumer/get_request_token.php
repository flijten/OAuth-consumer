<?php
$requestUrl 	= 'http://oauth.freek/oauth/provider/request_token.php';
$authorizeUrl   = 'http://oauth.freek/oauth/provider/authorize.php';
$callbackUrl    = 'http://oauth.freek/oauth/consumer/get_access_token.php';

$consumerKey 	= 'c6a9e2ced8e93fac830615ee8a0995448b53cc53';
$consumerSecret = 'd570714e748a585d85188fac92004f7508a13093';

session_start();
try {
	$OAuth              = new OAuth($consumerKey, $consumerSecret);
	$tokenInfo          = $OAuth->getRequestToken(
		$requestUrl .
		'?oauth_callback=' .
		$callbackUrl .
		'&scope=all'
	);

	echo '<pre>';
	var_dump($tokenInfo);
	echo '</pre>';
} catch (Exception $E) {
	echo '<pre>';
	var_dump($E->getMessage());
	var_dump($OAuth->getLastResponse());
	var_dump($OAuth->getLastResponseInfo());
	echo '</pre>';
}



$_SESSION['oauth_token_secret'] = $tokenInfo['oauth_token_secret'];

$location = $authorizeUrl . '?oauth_token=' . $tokenInfo['oauth_token'];
header('Location: ' . $location);
