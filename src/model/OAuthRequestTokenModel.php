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

class OAuthRequestTokenModel extends ModelBase
{

//OAuth access token specific fields

	/**
	 * @var int
	 */
	private $tokenId 				= null;
	/**
	 * @var string
	 */
	private $token					= null;
	/**
	 * @var string
	 */
	private $tokenSecret			= null;
	/**
	 * @var string
	 */
	private $tokenVerificationCode	= null;
	/**
	 * @var int
	 */
	private $tokenUserId			= null;
	/**
	 * @var int (timestamp)
	 */
	private $tokenDate				= null;
	/**
	 * @var string
	 */
	private $tokenConsumerKey		= null;
	/**
	 * @var string
	 */
	private $tokenCallback			= null;
	/**
	 * @var string
	 */
	private $tokenScope				= null;

//methods

	/**
	 * Serves as factory method. Loads the data for a request token based on the token
	 * string.
	 *
	 * @static
	 * @throws 	DataStoreReadException
	 * @param 	$token
	 * @param 	$DataStore
	 * @return 	OAuthRequestTokenModel
	 */
	public static function loadFromToken($token, $DataStore)
	{
		$sql = "SELECT *
				FROM `oauth_provider_request_token`
				WHERE `request_token` = '" . $DataStore->real_escape_string($token) . "'";

		$result = $DataStore->query($sql);

		if (!$result || $result->num_rows < 1) {
			throw new DataStoreReadException("Couldn't read the request token data from the datastore");
		}

		$data 	= $result->fetch_assoc();
		$result->close();

		$RequestToken = new OAuthRequestTokenModel($DataStore);
		$RequestToken->tokenId = $data['request_token_id'];
		$RequestToken->token = $data['request_token'];
		$RequestToken->tokenSecret = $data['request_token_secret'];
		$RequestToken->tokenVerificationCode = $data['request_token_verification_code'];
		$RequestToken->tokenUserId = $data['request_token_user_id'];
		$RequestToken->tokenDate = $data['request_token_date'];
		$RequestToken->tokenConsumerKey = $data['request_token_consumer_key'];
		$RequestToken->tokenCallback = $data['request_token_callback'];
		$RequestToken->tokenScope = $data['request_token_scope'];

		return $RequestToken;
	}

//CRUD

	/**
	 * Creates a new row for this Access token in the datastore. MUST set the field tokenId here.
	 *
	 * @throws DataStoreReadException
	 * @return void
	 */
	protected function create()
	{
		$sql = "INSERT INTO `oauth_provider_request_token`
				SET `request_token` = '" . $this->DataStore->real_escape_string($this->token) . "',
					`request_token_secret` = '" . $this->DataStore->real_escape_string($this->tokenSecret) . "',
					`request_token_verification_code` = '" . $this->DataStore->real_escape_string($this->tokenVerificationCode) . "',
					`request_token_user_id` = '" . $this->DataStore->real_escape_string($this->tokenUserId) . "',
					`request_token_date` = '" . $this->DataStore->real_escape_string($this->tokenDate) . "',
					`request_token_consumer_key` = '" . $this->DataStore->real_escape_string($this->tokenConsumerKey) . "',
					`request_token_callback` = '" . $this->DataStore->real_escape_string($this->tokenCallback) . "',
					`request_token_scope` = '" . $this->DataStore->real_escape_string($this->tokenScope) . "'";

		if ($this->DataStore->query($sql)) {
			$this->tokenId = $this->DataStore->insert_id;
		} else {
			throw new DataStoreReadException("Couldn't create the request token data in the datastore");
		}
	}

	/**
	 * Reads and returns the data for the access token with id $tokenId
	 *
	 * @throws 	DataStoreReadException
	 * @return 	array $data  An associative array with the data for $tokenId
	 */
	protected function read()
	{
		$sql = "SELECT request_token_id`, `request_token`, `request_token_secret`, `request_token_verification_code`,
					`request_token_user_id`, `request_token_date`, `request_token_consumer_key`, `request_token_callback`,
					`request_token_scope`
				FROM `oauth_provider_request_token`
				WHERE `request_token_id` = '" . $this->DataStore->real_escape_string($this->tokenId) . "'";

		$result = $this->DataStore->query($sql);

		if (!$result) {
			throw new DataStoreReadException("Couldn't read the request token data from the datastore");
		}

		$data 	= $result->fetch_assoc();
		$result->close();

		return $data;
	}

	/**
	 * Updates the row in the datastore for this OAuth access token.
	 *
	 * @throws DataStoreReadException
	 * @return void
	 */
	protected function update()
	{
		$sql = "UPDATE `oauth_provider_request_token`
				SET `request_token` = '" . $this->DataStore->real_escape_string($this->token) . "',
					`request_token_secret` = '" . $this->DataStore->real_escape_string($this->tokenSecret) . "',
					`request_token_verification_code` = '" . $this->DataStore->real_escape_string($this->tokenVerificationCode) . "',
					`request_token_user_id` = '" . $this->DataStore->real_escape_string($this->tokenUserId) . "',
					`request_token_date` = '" . $this->DataStore->real_escape_string($this->tokenDate) . "',
					`request_token_consumer_key` = '" . $this->DataStore->real_escape_string($this->tokenConsumerKey) . "',
					`request_token_callback` = '" . $this->DataStore->real_escape_string($this->tokenCallback) . "',
					`request_token_scope` = '" . $this->DataStore->real_escape_string($this->tokenScope) . "'
				WHERE `request_token_id` = '" . $this->DataStore->real_escape_string($this->tokenId) . "'";

		if (!$this->DataStore->query($sql)) {
			throw new DataStoreReadException("Couldn't update the request token data in the datastore");
		}
	}

	/**
	 * Deletes the row in the datastore for this OAuth access token
	 *
	 * @throws DataStoreReadException
	 * @return void
	 */
	public function delete()
	{
		$sql = "DELETE FROM `oauth_provider_request_token`
				WHERE `request_token_id` = '" . $this->DataStore->real_escape_string($this->tokenId) . "'";

		if (!$this->DataStore->query($sql)) {
			throw new DataStoreReadException("Couldn't delete the request token data in the datastore");
		}
	}

//getters and setters

	/**
	 * @param string $token
	 */
	public function setToken($token)
	{
		$this->token = $token;
	}

	/**
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * @param string $tokenCallback
	 */
	public function setTokenCallback($tokenCallback)
	{
		$this->tokenCallback = $tokenCallback;
	}

	/**
	 * @return string
	 */
	public function getTokenCallback()
	{
		return $this->tokenCallback;
	}

	/**
	 * @param string $tokenConsumerKey
	 */
	public function setTokenConsumerKey($tokenConsumerKey)
	{
		$this->tokenConsumerKey = $tokenConsumerKey;
	}

	/**
	 * @return string
	 */
	public function getTokenConsumerKey()
	{
		return $this->tokenConsumerKey;
	}

	/**
	 * @param int (timestamp) $tokenDate
	 */
	public function setTokenDate($tokenDate)
	{
		$this->tokenDate = $tokenDate;
	}

	/**
	 * @return int (timestamp)
	 */
	public function getTokenDate()
	{
		return $this->tokenDate;
	}

	/**
	 * @param int $tokenId
	 */
	public function setId($tokenId)
	{
		$this->tokenId = $tokenId;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->tokenId;
	}

	/**
	 * @param string $tokenScope
	 */
	public function setTokenScope($tokenScope)
	{
		$this->tokenScope = $tokenScope;
	}

	/**
	 * @return string
	 */
	public function getTokenScope()
	{
		return $this->tokenScope;
	}

	/**
	 * @param string $tokenSecret
	 */
	public function setTokenSecret($tokenSecret)
	{
		$this->tokenSecret = $tokenSecret;
	}

	/**
	 * @return string
	 */
	public function getTokenSecret()
	{
		return $this->tokenSecret;
	}

	/**
	 * @param int $tokenUserId
	 */
	public function setTokenUserId($tokenUserId)
	{
		$this->tokenUserId = $tokenUserId;
	}

	/**
	 * @return int
	 */
	public function getTokenUserId()
	{
		return $this->tokenUserId;
	}

	/**
	 * @param string $tokenVerificationCode
	 */
	public function setTokenVerificationCode($tokenVerificationCode)
	{
		$this->tokenVerificationCode = $tokenVerificationCode;
	}

	/**
	 * @return string
	 */
	public function getTokenVerificationCode()
	{
		return $this->tokenVerificationCode;
	}


}