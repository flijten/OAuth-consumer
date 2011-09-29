<?php
/**
 *
 * Copyright (c) 2011 Freek Lijten <freeklijten@gmail.com
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Freek Lijten nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author	Freek Lijten
 * @license BSD License
 */

class OAuthProviderWrapper
{
	const TOKEN_REQUEST = 0; //try to get a request token
	const TOKEN_ACCESS	= 1; //try to get an access token
	const TOKEN_VERIFY	= 2; //try to verify an access token so an API call can be made

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

			$this->Provider->tokenHandler(array($this,'checkRequestToken'));

			try {
				$this->Provider->checkOAuthRequest();
			} catch (Exception $Exception) {
				echo $Exception->getMessage();
				exit;
			}

		} else if ($mode == self::TOKEN_VERIFY) {

			$this->Provider->tokenHandler(array($this,'checkAccessToken'));

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

			try {
				$RequestToken->save();
			} catch (DataStoreCreateException $Exception) {
				echo $Exception->getMessage();
				exit;
			}

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
				#TODO throw exception?
			}
			#TODO the request token must be verified
			#TODO consumer key provided ($this->Provider->consumer_key) must be the consumer key belonging to $RequestToken

			$token = bin2hex($this->Provider->generateToken(15, true));
			$tokenSecret = bin2hex($this->Provider->generateToken(5, true));

			$AccessToken = new OAuthAccessTokenModel(Configuration::getDataStore());
			$AccessToken->setAccessToken($token);
			$AccessToken->setAccessTokenSecret($tokenSecret);
			$AccessToken->setAccessTokenDate(time());
			$AccessToken->setAccessTokenConsumerKey($this->Provider->consumer_key);
			$AccessToken->setAccessTokenUserId($RequestToken->getTokenUserId());
			$AccessToken->setAccessTokenScope($RequestToken->getTokenScope());

			try {
				$AccessToken->save();
				#TODO, if saved, remove request token from database (or invalidate it somehow)
			} catch (DataStoreCreateException $Exception) {
				echo $Exception->getMessage();
				exit;
			}

			echo "oauth_token=$token&oauth_token_secret=$tokenSecret";
			exit;
		} catch (Exception $E) {
			echo 'failed to create access token: ' .  $E->getMessage();
			exit;
		}
	}

	/**
	 * Returns the user Id for the currently authorized user
	 *
	 * @return int
	 */
	public function getUserId()
	{
		$AccessToken = OAuthAccessTokenModel::loadFromToken($this->Provider->token, Configuration::getDataStore());
		return $AccessToken->getAccessTokenUserId();
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

		try {
			$OAuthNonce->save();
		} catch (DataStoreCreateException $Exception) {
			return OAUTH_BAD_NONCE;
		}

		return OAUTH_OK;
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
			#TODO add blacklisting and/or throttling?
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
	public static function checkRequestToken($Provider)
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

	/**
	 * Checks if there is token information for the provided access token and sets the secret if it can be found.
	 *
	 * @static
	 * @param 	$Provider
	 * @return 	int
	 */
	public static function checkAccessToken($Provider)
	{
		try {
			$DataStore = Configuration::getDataStore();
		} catch (DataStoreConnectException $Exception) {
			// Ideally this exception should be rethrown here but the internals of PECL's OAuth class throw an exception
			// when a non-accepted return value (or no return value) is received. This seems to be winning from exceptions
			// thrown at this point.
			return OAUTH_TOKEN_REJECTED;
		}

		$AccessToken = OAuthAccessTokenModel::loadFromToken($Provider->token, $DataStore);

		if (!$AccessToken) {
			return OAUTH_TOKEN_REJECTED;
		}

		$Provider->token_secret = $AccessToken->getAccessTokenSecret();
		return OAUTH_OK;
	}
}