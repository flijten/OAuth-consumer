<?php
/**
 * @Author: Freek Lijten <freek@procurios.nl>
 */

$DB = new mysqli('localhost', 'root', null, 'test');

function newConsumerInfo($DB)
{
	$handle = fopen('/dev/urandom', 'rb');
	$random = fread($handle, 200);
	fclose($handle);

	$consumerKey = sha1(substr($random, 0, 100));
	$consumerSecret = sha1(substr($random, 100, 100));

	$sql = "INSERT INTO `oauth_consumer`
			SET consumer_key = '" . $DB->real_escape_string($consumerKey) . "',
				consumer_secret = '" . $DB->real_escape_string($consumerSecret) . "',
				consumer_create_date = '" . $DB->real_escape_string(time()) . "'";

	if ($DB->query($sql)) {
		return array('key' => $consumerKey, 'secret' => $consumerSecret);
	}
	return false;
}



echo '<pre>';
var_dump(newConsumerInfo($DB));
echo '</pre>';
