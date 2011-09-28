<?php
/**
 * @Author	Freek Lijten
 */

//non
if (!isset($_GET['oauth_token'])) {
	echo "No token supplied.";
	exit;
}

require_once(__DIR__ . '/../../lib/AutoLoader.php');
new AutoLoader();

$RequestToken = OAuthRequestTokenModel::loadFromToken($_GET['oauth_token'], Configuration::getDataStore());

if (!$RequestToken) {
	echo "Invalid token";
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['allow'])) {

	$verificationCode = substr( sha1( microtime() . rand(1000, 10000000) ), 5, 15 );

	$RequestToken->setTokenVerificationCode($verificationCode);
	$RequestToken->save();

	if (true) {
		#TODO save must return something probably
		header( 'location: ' . $RequestToken->getTokenCallback() . '?oauth_token=' . $RequestToken->getToken() . '&oauth_verifier=' . $verificationCode );
	}
}

echo "
	<form method='post' action='?oauth_token=" . $RequestToken->getToken() . "'>
		<input name='allow' type='submit' value='Allow'>
		<input name='deny' type='submit' value='Deny'>
	</form>";
