<?php
/**
 * @author      Freek Lijten <freek@procurios.nl>
 */

require_once(__DIR__ . '/../../lib/AutoLoader.php');
new AutoLoader();

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_VERIFY	);
$response 	= $Provider->checkOAuthRequest();
if ($response !== true) {
	echo $response;
	exit;
}

try {
	$userId = $Provider->getUserId();
} catch (ProviderException $Exception) {
	$Exception->getMessage();
}

$sql = "SELECT * FROM `user_messages` WHERE `user_id` = '" . $userId . "'";

$result = Configuration::getDataStore()->query($sql);
$returnValue = "<messages>";

while ($row = $result->fetch_assoc()) {
	$returnValue .= "<message>" . $row['message_text'] . "</message>";
}

$returnValue .= "</messages>";
//Token is valid, lets output something
echo $returnValue;
