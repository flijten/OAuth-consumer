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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['allow'])) {

	// User has no model, it just here by example, hence the open MySQL query
	// This is not a good way to actually store user data (plaintext password wtf)
	$DB 	= Configuration::getDataStore();
	$sql 	= "SELECT `user_id`, `user_name`, `user_password` FROM `user` WHERE `user_name` = '" . $DB->real_escape_string($_POST['user_name']) . "'";
	$result = $DB->query($sql);
	$row 	= $result->fetch_assoc();
	$result->close();

	if ($row['user_password'] != $_POST['user_password']) {
		echo "You hacker, be gone!";
		exit;
	}

	$verificationCode = OAuthProviderWrapper::generateToken();
	$RequestToken->setTokenVerificationCode($verificationCode);
	$RequestToken->setTokenUserId($row['user_id']);

	try {
		$RequestToken->save();
	} catch (DataStoreUpdateException $Exception) {
		echo $Exception->getMessage();
		exit;
	}

	header( 'location: ' . $RequestToken->getTokenCallback() . '?oauth_token=' . $RequestToken->getToken() . '&oauth_verifier=' . $verificationCode );

} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['allow'])) {

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
