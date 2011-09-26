<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @Author	Freek Lijten
 */

$accessURL	 	= 'http://oauth.freek/oauth/provider/access_token.php';
$consumerKey 	= 'c6a9e2ced8e93fac830615ee8a0995448b53cc53';
$consumerSecret = 'd570714e748a585d85188fac92004f7508a13093';

session_start();
try {
	$OAuth              = new OAuth($consumerKey, $consumerSecret);
	$OAuth->setToken($_GET['oauth_token'], $_SESSION['oauth_token_secret']);
	$tokenInfo          = $OAuth->getAccessToken($accessURL . '?oauth_verifier=' . $_GET['oauth_verifier']);

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

