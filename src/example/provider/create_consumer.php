<?php
/**
 * @Author	Freek Lijten
 */
require_once(__DIR__ . '/../../lib/AutoLoader.php');
new AutoLoader();

//create consumer model
$Consumer = new OAuthConsumerModel(Configuration::getDataStore());
$Consumer->setConsumerCreateDate(time());
$Consumer->setConsumerKey(OAuthProviderWrapper::generateToken());
$Consumer->setConsumerSecret(OAuthProviderWrapper::generateToken());
$Consumer->save();

echo "Consumer key: $consumerKey <br />Consumer secret: $consumerSecret";