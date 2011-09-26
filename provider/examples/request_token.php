<?php
/**
 * @Author	Freek Lijten
 */

require_once(__DIR__ . '/lib/AutoLoader.php');
new AutoLoader();

$Provider = new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_REQUEST);
$Provider->outputRequestToken();







return;
$Request = new RequestTokenProvider();

class RequestTokenProvider
{
	function __construct()
	{
		$Provider = new OAuthProvider();

		$Provider->consumerHandler(array($this,'consumerHandler'));
		$Provider->timestampNonceHandler(array($this,'timestampNonceHandler'));
		$Provider->isRequestTokenEndpoint(true);

		try {
			$Provider->checkOAuthRequest();
			$DB = new mysqli('localhost', 'root', '', 'test');

			$requestToken = bin2hex($Provider->generateToken(30, true));
			$requestTokenSecret = bin2hex($Provider->generateToken(10, true));

			$sql = "INSERT INTO `oauth_request_token`
					SET `request_token` = '" . $DB->real_escape_string($requestToken) . "',
						`request_token_secret` = '" . $DB->real_escape_string($requestTokenSecret) . "',
						`request_token_date` = '" . $DB->real_escape_string(time()) . "',
						`consumer_key` = '" . $DB->real_escape_string($Provider->consumer_key) . "',
						`callback` = '" . $DB->real_escape_string($_GET['oauth_callback']) . "',
						`scope` = '" . $DB->real_escape_string($_GET['scope']) . "'";

			$DB->query($sql);
			echo "oauth_token=$requestToken&oauth_token_secret=$requestTokenSecret&oauth_callback_confirmed=true";
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
		$now = time();
		//timestamp is off too much (5 mins+), refuse token
		if ($now - $Provider->timestamp > 300) {
			return OAUTH_BAD_TIMESTAMP;
		}

		$DB = new mysqli('localhost', 'root', '', 'test');

		if (mysqli_connect_error()) {
			echo mysqli_connect_error();exit;
		}

		$sql = "SELECT 1
				FROM `oauth_nonce`
				WHERE `nonce` = '" . $DB->real_escape_string($Provider->nonce) . "'
					AND `nonce_consumer_key` = '" . $Provider->consumer_key . "'";

		$result = $DB->query($sql);

		if ($result->num_rows == 0) {
			$sql = "INSERT INTO `oauth_nonce`
					SET `nonce` = '" . $DB->real_escape_string($Provider->nonce) . "',
						`nonce_consumer_key` = '" . $DB->real_escape_string($Provider->consumer_key) . "',
						`nonce_date` = '" . time() . "'";
			if ($DB->query($sql)) {
				return OAUTH_OK;
			}
		} else {
			$result->free();
		}
		return OAUTH_BAD_NONCE;
	}

	public static function consumerHandler($Provider)	{

		$Provider->addRequiredParameter("oauth_callback");
		$Provider->addRequiredParameter("scope");

		$DB = new mysqli('localhost', 'root', null, 'test');

		$sql = "SELECT `consumer_secret` FROM `oauth_consumer` WHERE `consumer_key` = '" . $Provider->consumer_key . "'";
		$result = $DB->query($sql);
		$row = $result->fetch_assoc();
		$result->free();

		if (!empty($row['consumer_secret'])) {
			$Provider->consumer_secret = $row['consumer_secret'];
			return OAUTH_OK;
		}
#todo ADD BLACKLISTING
		return OAUTH_CONSUMER_KEY_UNKNOWN;
	}
}