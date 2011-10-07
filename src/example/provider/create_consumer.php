<?php
/**
 * @Author	Freek Lijten
 */
require_once(__DIR__ . '/../../lib/AutoLoader.php');
new AutoLoader();

//generate random keys
$handle = fopen('/dev/urandom', 'rb');
$random = fread($handle, 200);
fclose($handle);
$consumerKey = sha1(substr($random, 0, 100));
$consumerSecret = sha1(substr($random, 100, 100));

//create consumer model
$Consumer = new OAuthConsumerModel(Configuration::getDataStore());
$Consumer->setConsumerCreateDate(time());
$Consumer->setConsumerKey($consumerKey);
$Consumer->setConsumerSecret($consumerSecret);
$Consumer->save();

echo "Consumer key: $consumerKey <br />Consumer secret: $consumerSecret";