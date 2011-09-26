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
			$RequestToken->save();

			echo "oauth_token=$token&oauth_token_secret=$tokenSecret&oauth_callback_confirmed=true";
			exit;
		} catch (Exception $E) {
			echo $E->getMessage();
			exit;
		}
	}

	/**
	 * Check and store nonce
	 *
	 * @param  $Provider
	 * @return void
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
			// when a non-accepted return value (or no return value) is received
			return OAUTH_BAD_NONCE;
		}

		if (OAuthNonceModel::nonceExists($Provider->nonce, $DataStore)) {
			return OAUTH_BAD_NONCE;
		}

		$OAuthNonce = new OAuthNonceModel(Configuration::getDataStore());
		$OAuthNonce->setNonce($Provider->nonce);
		$OAuthNonce->setNonceConsumerKey($Provider->consumer_key);
		$OAuthNonce->setNonceDate(time());
		$OAuthNonce->save();

		if (true) {
			#TODO save should return something or throw exceptions
			return OAUTH_OK;
		}
		return OAUTH_BAD_NONCE;
	}

	public static function consumerHandler($Provider)
	{
		$Provider->addRequiredParameter("oauth_callback");
		$Provider->addRequiredParameter("scope");

		try {
			$DataStore = Configuration::getDataStore();
		} catch (DataStoreConnectException $Exception) {
			// Ideally this exception should be rethrown here but the internals of PECL's OAuth class throw an exception
			// when a non-accepted return value (or no return value) is received
			return OAUTH_CONSUMER_KEY_UNKNOWN;
		}

		$OAuthConsumer = OAuthConsumerModel::loadFromConsumerKey($Provider->consumer_key, $DataStore);

		if ($OAuthConsumer) {
			$Provider->consumer_secret = $OAuthConsumer->getConsumerSecret();
			return OAUTH_OK;
		}

		#TODO ADD BLACKLISTING?
		return OAUTH_CONSUMER_KEY_UNKNOWN;
	}

	public static function tokenHandler($Provider)
	{
		/**
		 * 1. check request token exits
		 * 2. if so try to create an access token and save it.
		 * 3. OAuth OK or REJECT
		 *
		 *
		 */

		$DB = new mysqli('localhost', 'root', null, 'test');

		$sql = "SELECT `request_token_secret`, `request_token_verification_code` FROM `oauth_request_token` WHERE `request_token` = '" . $Provider->token . "'";
		$result = $DB->query($sql);
		$row = $result->fetch_assoc();
		$result->free();

		if (empty($row)) { //no token found?
			return OAUTH_TOKEN_REJECTED;
		}

		if ($row['request_token_verification_code'] != $_GET['oauth_verifier']) {
			return OAUTH_VERIFIER_INVALID;
		}

		$Provider->token_secret = $row['request_token_secret'];
		return OAUTH_OK;
	}
}