<?php
/**
 * @author      Freek Lijten <freek@procurios.nl>
 */

require_once(__DIR__ . '/lib/AutoLoader.php');
new AutoLoader();

try {
	$Provider = new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_VERIFY	);
} catch (Exception $E) {
	var_dump($E);
}

//Token is valid, lets output something
echo "your resource!";
