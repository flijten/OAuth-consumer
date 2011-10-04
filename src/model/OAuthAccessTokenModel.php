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

class OAuthAccessTokenModel extends ModelBase
{
	/**
	 * @var int
	 */
	private $accessTokenId 			= null;
	/**
	 * @var string
	 */
	private $accessToken			= null;
	/**
	 * @var string
	 */
	public $accessTokenSecret		= null;
	/**
	 * @var int
	 */
	private $accessTokenState		= null;
	/**
	 * @var int
	 */
	private $accessTokenUserId		= null;
	/**
	 * @var int
	 */
	private $accessTokenDate		= null;
	/**
	 * @var string
	 */
	private $accessTokenConsumerKey = null;
	/**
	 * @var string
	 */
	private $accessTokenScope		= null;

	/**
	 * Serves as factory method. Loads the data for a request token based on the token
	 * string.
	 *
	 * @static
	 * @throws 	DataStoreReadException
	 * @param 	$token
	 * @param 	$DataStore
	 * @return 	OAuthAccessTokenModel
	 */
	public static function loadFromToken($token, $DataStore)
	{
		$sql = "SELECT *
				FROM `oauth_provider_access_token`
				WHERE `access_token` = '" . $DataStore->real_escape_string($token) . "'";

		$result = $DataStore->query($sql);

		if (!$result || $result->num_rows < 1) {
			throw new DataStoreReadException("Couldn't read the access token data from the datastore");
		}

		$data 	= $result->fetch_assoc();
		$result->close();

		$AccessToken = new OAuthAccessTokenModel($DataStore);
		$AccessToken->accessTokenId = $data['access_token_id'];
		$AccessToken->accessToken = $data['access_token'];
		$AccessToken->accessTokenSecret = $data['access_token_secret'];
		$AccessToken->accessTokenUserId = $data['access_token_user_id'];
		$AccessToken->accessTokenDate = $data['access_token_date'];
		$AccessToken->accessTokenConsumerKey = $data['access_token_consumer_key'];
		$AccessToken->accessTokenScope = $data['access_token_scope'];

		return $AccessToken;
	}

// CRUD

	/**
	 * @throws DataStoreCreateException
	 * @return void
	 */
	protected function create()
	{
		$sql = "INSERT INTO `oauth_provider_access_token`
				SET `access_token` = '" . $this->DataStore->real_escape_string($this->accessToken) . "',
					`access_token_secret` = '" . $this->DataStore->real_escape_string($this->accessTokenSecret) . "',
					`access_token_user_id` = '" . $this->DataStore->real_escape_string($this->accessTokenUserId) . "',
					`access_token_date` = '" . $this->DataStore->real_escape_string($this->accessTokenDate) . "',
					`access_token_consumer_key` = '" . $this->DataStore->real_escape_string($this->accessTokenConsumerKey) . "',
					`access_token_scope` = '" . $this->DataStore->real_escape_string($this->accessTokenScope) . "'";

		if ($this->DataStore->query($sql)) {
			$this->tokenId = $this->DataStore->insert_id;
		} else {
			throw new DataStoreCreateException("Couldn't save the access token to the datastore");
		}
	}

	/**
	 * @throws DataStoreReadException
	 * @return
	 */
	protected function read()
	{
		$sql = "SELECT *
				FROM `oauth_provider_access_token
				WHERE `access_token_id` = '" . $this->DataStore->real_escape_string($this->accessTokenId) . "'";

		$result = $this->DataStore->query($sql);

		if (!$result) {
			throw new DataStoreReadException("Couldn't read the access token data from the datastore");
		}

		$data 	= $result->fetch_assoc();
		$result->close();

		return $data;
	}

	/**
	 * @throws DataStoreUpdateException
	 * @return void
	 */
	protected function update()
	{
		$sql = "UPDATE `oauth_provider_access_token`
				SET `access_token` = '" . $this->DataStore->real_escape_string($this->accessToken) . "',
					`access_token_secret` = '" . $this->DataStore->real_escape_string($this->accessTokenSecret) . "',
					`access_token_user_id` = '" . $this->DataStore->real_escape_string($this->accessTokenUserId) . "',
					`access_token_date` = '" . $this->DataStore->real_escape_string($this->accessTokenDate) . "',
					`access_token_consumer_key` = '" . $this->DataStore->real_escape_string($this->accessTokenConsumerKey) . "',
					`access_token_scope` = '" . $this->DataStore->real_escape_string($this->accessTokenScope) . "'
				WHERE `access_token_id` = '" . $this->DataStore->real_escape_string($this->accessTokenId) . "'";

		if (!$this->DataStore->query($sql)) {
			throw new DataStoreUpdateException("Couldn't update the access token to the datastore");
		}
	}

	/**
	 * @throws DataStoreDeleteException
	 * @return void
	 */
	public function delete()
	{
		$sql = "DELETE FROM `oauth_provider_access_token`
				WHERE `access_token_id` = '" . $this->DataStore->real_escape_string($this->accessTokenId) . "'";

		if (!$this->DataStore->query($sql)) {
			throw new DataStoreDeleteException("Couldn't delete the access token from the datastore");
		}
	}

// Getters and setters

	/**
	 * @param string $accessToken
	 */
	public function setAccessToken($accessToken)
	{
		$this->accessToken = $accessToken;
	}

	/**
	 * @return string
	 */
	public function getAccessToken()
	{
		return $this->accessToken;
	}

	/**
	 * @param string $accessTokenConsumerKey
	 */
	public function setAccessTokenConsumerKey($accessTokenConsumerKey)
	{
		$this->accessTokenConsumerKey = $accessTokenConsumerKey;
	}

	/**
	 * @return string
	 */
	public function getAccessTokenConsumerKey()
	{
		return $this->accessTokenConsumerKey;
	}

	/**
	 * @param int $accessTokenDate
	 */
	public function setAccessTokenDate($accessTokenDate)
	{
		$this->accessTokenDate = $accessTokenDate;
	}

	/**
	 * @return int
	 */
	public function getAccessTokenDate()
	{
		return $this->accessTokenDate;
	}

	/**
	 * @param int $tokenId
	 */
	public function setId($tokenId)
	{
		$this->accessTokenId = $tokenId;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->accessTokenId;
	}

	/**
	 * @param string $accessTokenScope
	 */
	public function setAccessTokenScope($accessTokenScope)
	{
		$this->accessTokenScope = $accessTokenScope;
	}

	/**
	 * @return string
	 */
	public function getAccessTokenScope()
	{
		return $this->accessTokenScope;
	}

	/**
	 * @param string $accessTokenSecret
	 */
	public function setAccessTokenSecret($accessTokenSecret)
	{
		$this->accessTokenSecret = $accessTokenSecret;
	}

	/**
	 * @return string
	 */
	public function getAccessTokenSecret()
	{
		return $this->accessTokenSecret;
	}

	/**
	 * @param int $accessTokenState
	 */
	public function setAccessTokenState($accessTokenState)
	{
		$this->accessTokenState = $accessTokenState;
	}

	/**
	 * @return int
	 */
	public function getAccessTokenState()
	{
		return $this->accessTokenState;
	}

	/**
	 * @param int $accessTokenUserId
	 */
	public function setAccessTokenUserId($accessTokenUserId)
	{
		$this->accessTokenUserId = $accessTokenUserId;
	}

	/**
	 * @return int
	 */
	public function getAccessTokenUserId()
	{
		return $this->accessTokenUserId;
	}
}