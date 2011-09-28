<?php
/**
 * @Author	Freek Lijten
 */
require_once(__DIR__ . '/../../lib/AutoLoader.php');
new AutoLoader();

$Provider = new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_REQUEST);
$Provider->outputRequestToken();