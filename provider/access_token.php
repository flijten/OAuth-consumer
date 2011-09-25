<?php
/**
 * @Author: Freek Lijten <freek@procurios.nl>
 * @Package:
 */
$AccessTokenProvider = new AccessTokenProvider();

class AccessTokenProvider
{
	function __construct()
	{
		$Provider = new OAuthProvider();

		$Provider->consumerHandler(array($this,'consumerHandler'));
		$Provider->timestampNonceHandler(array($this,'timestampNonceHandler'));
		$Provider->tokenHandler(array($this,'tokenHandler'));


		try {
			$Provider->checkOAuthRequest();


			echo "end!";exit;
			$DB = new mysqli('localhost', 'root', '', 'test');
			$sql = "SELECT `scope`, `request_token_user_id` FROM `oauth_request_token` WHERE `request_token` = '" . $Provider->token . "'";
			$result = $DB->query($sql);
			$row = $result->fetch_assoc();
			$result->free();

			$accessToken = bin2hex($Provider->generateToken(20, true));
			$accessTokenSecret = bin2hex($Provider->generateToken(20, true));

			$sql = "INSERT INTO `oauth_access_token`
					SET `access_token` = '" . $DB->real_escape_string($accessToken) . "',
						`access_token_secret` = '" . $DB->real_escape_string($accessTokenSecret) . "',
						`access_token_date` = '" . $DB->real_escape_string(time()) . "',
						`consumer_key` = '" . $DB->real_escape_string($Provider->consumer_key) . "',
						`request_token_user_id` = '" . $DB->real_escape_string($row['request_token_user_id']) . "',
						`scope` = '" . $DB->real_escape_string($row['scope']) . "'";
#TODO set state of request token to consumer
			

			$DB->query($sql);
			echo "oauth_token=$requestToken&oauth_token_secret=$requestTokenSecret";
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

	public static function consumerHandler($Provider)
	{
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

	public static function tokenHandler($Provider)
	{
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