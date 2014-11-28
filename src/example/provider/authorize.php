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

try {
	$RequestToken = OAuthRequestTokenModel::loadFromToken($_GET['oauth_token'], Configuration::getDataStore());
} catch (DataStoreReadException $Exception) {
	echo $Exception->getMessage();
	exit;
}

$username = "Jason";
$userPassword = "pas";
$userId = 12345;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['allow'])) {

	if ($_POST['user_name'] !== $username || $_POST["user_password"] !== $userPassword) {

		echo "Invalid credentials";
		exit;
	} 

	$verificationCode = OAuthProviderWrapper::generateToken();
	$RequestToken->setTokenVerificationCode($verificationCode);
	$RequestToken->setTokenUserId("aaaab");

	try {
		$RequestToken->save();
	} catch (DataStoreUpdateException $Exception) {
		echo $Exception->getMessage();
		exit;
	}

	header( 'location: ' . $RequestToken->getTokenCallback() . '?oauth_token=' . $RequestToken->getToken() . '&oauth_verifier=' . $verificationCode );

} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['deny'])) {

	//The user specifically denied access. Lets delete the request token
	try {
		$RequestToken->delete();
	} catch (DataStoreDeleteException $Exception) {
		echo $Exception->getMessage();
		exit;
	}
}

echo "
	<form method='post' action='?oauth_token=" . $RequestToken->getToken() . "'>
		username: <input name='user_name' type='input'><br />
		password: <input name='user_password' type='input'><br />
		<input name='allow' type='submit' value='Allow'>
		<input name='deny' type='submit' value='Deny'>
	</form>";
