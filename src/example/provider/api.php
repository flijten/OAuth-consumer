<?php
/**
 * @author      Freek Lijten <freek@procurios.nl>
 */

require_once(__DIR__ . '/../../lib/AutoLoader.php');
new AutoLoader();

try {
	$Provider = new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_VERIFY	);
} catch (Exception $E) {
	var_dump($E);
}

$userId = $Provider->getUserId();

$sql = "SELECT * FROM `user_messages` WHERE `user_id` = '" . $userId . "'";

$result = Configuration::getDataStore()->query($sql);
$returnValue = "<messages>";

while ($row = $result->fetch_assoc()) {
	$returnValue .= "<message>" . $row['message_text'] . "</message>";
}

$returnValue .= "</messages>";
//Token is valid, lets output something
echo $returnValue;
