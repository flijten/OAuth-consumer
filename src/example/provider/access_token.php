<?php
/**
 * @Author	Freek Lijten
 */
require_once(__DIR__ . '/../../lib/AutoLoader.php');
new AutoLoader();

$Provider = new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_ACCESS	);
try {
	$Provider->outputAccessToken();
} catch (ProviderException $Exception) {
	echo $Exception->getMessage();
}