<?php
/**
 * @Author: Freek Lijten <freek@procurios.nl>
 */


if (!isset($_GET['oauth_token'])) {
	echo "No token supplied.";
//	return;
}

$DB = new mysqli('localhost', 'root', '', 'test');
$sql = "SELECT `request_token_id`, `callback`
		FROM `oauth_request_token`
		WHERE `request_token` = '" .  $DB->real_escape_string($_GET['oauth_token']) . "'
		AND `request_token_verification_code` IS NULL";

$result = $DB->query($sql);

if ($result->num_rows != 1) {
	echo "Token is not valid.";
//	return;
}

$row = $result->fetch_assoc();
$id = $row['request_token_id'];
$callback = $row['callback'];
$result->free();

var_dump($callback);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['allow'])) {

	$verificationCode = substr( sha1( microtime() . rand(1000, 10000000) ), 5, 15 );

	$sql = "UPDATE `oauth_request_token`
			SET `request_token_verification_code` = '" . $DB->real_escape_string($verificationCode) . "'";

	if ($DB->query($sql)) {
		header( 'location: ' . $callback . '?oauth_token=' . $_GET['oauth_token'] . '&oauth_verifier=' . $verificationCode );
	}
}

echo "
	<form method='post' action='?oauth_token=" . $_GET['oauth_token'] . "'>
		<input name='allow' type='submit' value='Allow'>
		<input name='deny' type='submit' value='Deny'>
	</form>";
