<?php
/**
 * @Author	Freek Lijten
 */
require_once(__DIR__ . '/../../lib/AutoLoader.php');
new AutoLoader();

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_REQUEST);
$response 	= $Provider->checkOAuthRequest();
if ($response !== true) {
	echo $response;
	exit;
}

try {
	$Provider->outputRequestToken();
} catch (ProviderException $Exception) {
	echo $Exception->getMessage();
}
exit;