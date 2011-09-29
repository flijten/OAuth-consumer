<?php
/**
 * Config file used to 'setup' the basic example with your own data.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

// Consumer key and secret. Change these into the key and secret you generated
$consumerKey 	= 'aaaa8eb070955e55476ea103bc74f664ae0d540b';
$consumerSecret = '26ae0d26573f4f26aed5053b3d967128241e367a';

// Access token and secret. Change these into the ones you received at the end of the OAuth handshake cycle
$token			= '319f499ec8090e92e5ca0d188cc663';
$tokenSecret	= '025b6bd5c6';

// Endpoints, at least change the urls to where you left the endpoint scripts
$apiURL	 		= 'http://oauth.freek/oauth/src/example/provider/api.php';
$accessURL	 	= 'http://oauth.freek/oauth/src/example/provider/access_token.php';
$requestURL 	= 'http://oauth.freek/oauth/src/example/provider/request_token.php';
$authorizeURL   = 'http://oauth.freek/oauth/src/example/provider/authorize.php';
$callbackURL    = 'http://oauth.freek/oauth/src/example/consumer/get_access_token.php';