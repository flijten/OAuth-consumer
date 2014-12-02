<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

require_once __DIR__ . '/config.php';

session_start();

try {
	$OAuth	= new OAuth($consumerKey, $consumerSecret);
	$OAuth->setToken($token, $tokenSecret);
	$result = $OAuth->fetch($apiURL, array(), OAUTH_HTTP_METHOD_POST);

	echo $OAuth->getLastResponse();
} catch (Exception $E) {
	echo '<pre>';
	var_dump($E->getMessage());
	var_dump($OAuth->getLastResponse());
	var_dump($OAuth->getLastResponseInfo());
	echo '</pre>';
}