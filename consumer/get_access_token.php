<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @Author	Freek Lijten
 */

$accessURL	 	= 'http://oauth.freek/oauth/provider/access_token.php';
$consumerKey 	= 'b26594220a7728ad931bce3232ee22d2d2520ec6';
$consumerSecret = 'c21528fbf82c209045e85877636beddeb1ab4e48';

session_start();

try {
	$OAuth              = new OAuth($consumerKey, $consumerSecret);
	$OAuth->setToken($_GET['oauth_token'], $_SESSION['oauth_token_secret']);
	$tokenInfo          = $OAuth->getAccessToken($accessURL . '?oauth_verifier=' . $_GET['oauth_verifier']);

	echo '<pre>goei:
	';
	var_dump($tokenInfo);
	echo '</pre>';
} catch (Exception $E) {
	echo '<pre>owow:
	';
	var_dump($E->getMessage());
	var_dump($OAuth->getLastResponse());
	var_dump($OAuth->getLastResponseInfo());
	echo '</pre>';
}

