<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

$apiURL	 		= 'http://oauth.freek/oauth/provider/api.php';
$consumerKey 	= 'b26594220a7728ad931bce3232ee22d2d2520ec6';
$consumerSecret = 'c21528fbf82c209045e85877636beddeb1ab4e48';
$token			= '873cae8e638efe4d560f13e1065a28';
$tokenSecret	= '8264cf9e1d';
session_start();

try {
	$OAuth	= new OAuth($consumerKey, $consumerSecret);
	$OAuth->setToken($token, $tokenSecret);
	$result = $OAuth->fetch($apiURL, array(), OAUTH_HTTP_METHOD_POST);

	echo '<pre>';
	var_dump($OAuth->getLastResponse());
	echo '</pre>';
} catch (Exception $E) {
	echo '<pre>';
	var_dump($E->getMessage());
	var_dump($OAuth->getLastResponse());
	var_dump($OAuth->getLastResponseInfo());
	echo '</pre>';
}