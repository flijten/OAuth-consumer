<?php
/**
 * @author      Freek Lijten <freek@procurios.nl>
 */

require_once(__DIR__ . '/../../lib/AutoLoader.php');
new AutoLoader();

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_VERIFY);
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

echo " Hello User with user_id: " . $userId . " You have now access to this secured endpoint";