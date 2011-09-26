<?php
/**
 * @author	Freek Lijten
 */
#TODO evaluate all database fields for actual usage
class OAuthProviderWrapper
{
	const TOKEN_REQUEST = 0;
	const TOKEN_ACCESS	= 1;

	private $Provider;

	public function __construct($mode)
	{
		$this->Provider = new OAuthProvider();
		$this->Provider->consumerHandler(array($this,'consumerHandler'));
		$this->Provider->timestampNonceHandler(array($this,'timestampNonceHandler'));

		if ($mode == self::TOKEN_REQUEST) {

			$this->Provider->isRequestTokenEndpoint(true);

			try {
				$this->Provider->checkOAuthRequest();
			} catch (Exception $Exception) {
				echo $Exception->getMessage();
				exit;
			}

		} else if ($mode == self::TOKEN_ACCESS) {

			$this->Provider->tokenHandler(array($this,'tokenHandler'));

			try {
				$this->Provider->checkOAuthRequest();
			} catch (Exception $Exception) {
				echo $Exception->getMessage();
				exit;
			}
		}
	}

	public function outputRequestToken()
	{
		try {
			$token = bin2hex($this->Provider->generateToken(15, true));
			$tokenSecret = bin2hex($this->Provider->generateToken(5, true));

			$RequestToken = new OAuthRequestTokenModel(Configuration::getDataStore());
			$RequestToken->setToken($token);
			$RequestToken->setTokenSecret($tokenSecret);
			$RequestToken->setTokenDate(time());
			$RequestToken->setTokenConsumerKey($this->Provider->consumer_key);
			$RequestToken->setTokenCallback($_GET['oauth_callback']);
			$RequestToken->setTokenScope($_GET['scope']);
			#TODO userId?
			$RequestToken->save();

			echo "oauth_token=$token&oauth_token_secret=$tokenSecret&oauth_callback_confirmed=true";
			exit;
		} catch (Exception $E) {
			echo 'failed to create request token: ' .  $E->getMessage();
			exit;
		}
	}

	public function outputAccessToken()
	{
		try {
			$RequestToken = OAuthRequestTokenModel::loadFromToken($this->Provider->token, Configuration::getDataStore());

			if (!$RequestToken) {
				#throw exception?
			}

			$token = bin2hex($this->Provider->generateToken(15, true));
			$tokenSecret = bin2hex($this->Provider->generateToken(5, true));

			$AccessToken = new OAuthAccessTokenModel(Configuration::getDataStore());
			$AccessToken->setAccessToken($token);
			$AccessToken->setAccessTokenSecret($tokenSecret);
			#TODO state?
			$AccessToken->setAccessTokenDate(time());
			$AccessToken->setAccessTokenConsumerKey($this->Provider->consumer_key);
			$AccessToken->setAccessTokenUserId($RequestToken->getTokenUserId());
			$AccessToken->setAccessTokenScope($RequestToken->getTokenUserId());
			$AccessToken->save();

			echo "oauth_token=$token&oauth_token_secret=$tokenSecret";
			exit;
		} catch (Exception $E) {
			echo 'failed to create access token: ' .  $E->getMessage();
			exit;
		}
	}

	/**
	 * Checks if the nonce is valid and, if so, stores it in the DataStore.
	 * Used as a callback function
	 *
	 * @param  $Provider
	 * @return int
	 */
	public static function timestampNonceHandler($Provider)
	{
		// Timestamp is off too much (5 mins+), refuse token
		$now = time();
		if ($now - $Provider->timestamp > 300) {
			return OAUTH_BAD_TIMESTAMP;
		}

		try {
			$DataStore = Configuration::getDataStore();
		} catch (DataStoreConnectException $Exception) {
			// Ideally this exception should be rethrown here but the internals of PECL's OAuth class throw an exception
			// when a non-accepted return value (or no return value) is received. This seems to be winning from exceptions
			// thrown at this point.
			return OAUTH_BAD_NONCE;
		}

		if (OAuthNonceModel::nonceExists($Provider->nonce, $DataStore)) {
			return OAUTH_BAD_NONCE;
		}

		$OAuthNonce = new OAuthNonceModel(Configuration::getDataStore());
		$OAuthNonce->setId($Provider->nonce);
		$OAuthNonce->setNonceConsumerKey($Provider->consumer_key);
		$OAuthNonce->setNonceDate(time());
		$OAuthNonce->save();

		if (true) {
			#TODO save should return something or throw exceptions
			return OAUTH_OK;
		}
		return OAUTH_BAD_NONCE;
	}

	/**
	 * Checks if the provided consumer key is valid and sets the corresponding
	 * consumer secret. Used as a callback function.
	 *
	 * @static
	 * @param 	$Provider
	 * @return 	int
	 */
	public static function consumerHandler($Provider)
	{
		$Provider->addRequiredParameter("oauth_callback");
		$Provider->addRequiredParameter("scope");

		try {
			$DataStore = Configuration::getDataStore();
		} catch (DataStoreConnectException $Exception) {
			// Ideally this exception should be rethrown here but the internals of PECL's OAuth class throw an exception
			// when a non-accepted return value (or no return value) is received. This seems to be winning from exceptions
			// thrown at this point.
			return OAUTH_CONSUMER_KEY_UNKNOWN;
		}

		$OAuthConsumer = OAuthConsumerModel::loadFromConsumerKey($Provider->consumer_key, $DataStore);

		if (!$OAuthConsumer) {
			#TODO ADD BLACKLISTING?
			return OAUTH_CONSUMER_KEY_UNKNOWN;
		}

		$Provider->consumer_secret = $OAuthConsumer->getConsumerSecret();
		return OAUTH_OK;
	}

	/**
	 * Checks if there is token information for the provided token and sets the secret if it can be found.
	 *
	 * @static
	 * @param 	$Provider
	 * @return 	int
	 */
	public static function tokenHandler($Provider)
	{
		try {
			$DataStore = Configuration::getDataStore();
		} catch (DataStoreConnectException $Exception) {
			// Ideally this exception should be rethrown here but the internals of PECL's OAuth class throw an exception
			// when a non-accepted return value (or no return value) is received. This seems to be winning from exceptions
			// thrown at this point.
			return OAUTH_TOKEN_REJECTED;
		}

		$RequestToken = OAuthRequestTokenModel::loadFromToken($Provider->token, $DataStore);

		if (!$RequestToken) {
			return OAUTH_TOKEN_REJECTED;
		}

		if ($_GET['oauth_verifier'] != $RequestToken->getTokenVerificationCode()) {
			return OAUTH_VERIFIER_INVALID;
		}

		$Provider->token_secret = $RequestToken->getTokenSecret();
		return OAUTH_OK;
	}
}