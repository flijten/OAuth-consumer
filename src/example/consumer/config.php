<?php
/**
 * Config file used to 'setup' the basic example with your own data.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

// Consumer key and secret. Change these into the key and secret you generated
$consumerKey 	= 'b26594220a7728ad931bce3232ee22d2d2520ec6';
$consumerSecret = 'c21528fbf82c209045e85877636beddeb1ab4e48';

// Access token and secret. Change these into the ones you received at the end of the OAuth handshake cycle
$token			= '873cae8e638efe4d560f13e1065a28';
$tokenSecret	= '8264cf9e1d';

// Endpoints, at least change the urls to where you left the endpoint scripts
$apiURL	 		= 'http://oauth.freek/oauth/src/example/provider/api.php';
$accessURL	 	= 'http://oauth.freek/oauth/src/example/provider/access_token.php';
$requestURL 	= 'http://oauth.freek/oauth/src/example/provider/request_token.php';
$authorizeURL   = 'http://oauth.freek/oauth/src/example/provider/authorize.php';
$callbackURL    = 'http://oauth.freek/oauth/src/example/consumer/get_access_token.php';