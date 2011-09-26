<?php

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
	private $accessTokenSecret		= null;
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

// CRUD

	protected function create()
	{
		$sql = "INSERT INTO `oauth_provider_access_token`
				SET `access_token` = '" . $this->DataStore->real_escape_string($this->accessToken) . "',
					`access_token_secret` = '" . $this->DataStore->real_escape_string($this->accessTokenSecret) . "',
					`access_token_state` = '" . $this->DataStore->real_escape_string($this->accessTokenState) . "',
					`access_token_user_id` = '" . $this->DataStore->real_escape_string($this->accessTokenUserId) . "',
					`access_token_date` = '" . $this->DataStore->real_escape_string($this->accessTokenDate) . "',
					`access_token_consumer_key` = '" . $this->DataStore->real_escape_string($this->accessTokenConsumerKey) . "',
					`access_token_scope` = '" . $this->DataStore->real_escape_string($this->accessTokenScope) . "'";

		if ($this->DataStore->query($sql)) {
			$this->tokenId = $this->DataStore->insert_id;
		} else {
			#TODO throw exception?
		}
	}

	protected function read()
	{
		$sql = "SELECT *
				FROM `oauth_provider_access_token
				WHERE `access_token_id` = '" . $this->DataStore->real_escape_string($this->accessTokenId) . "'";

		$result = $this->DataStore->query($sql);
		$data 	= $result->fetch_assoc();
		$result->close();

		return $data;
	}

	protected function update()
	{
		$sql = "UPDATE `oauth_provider_access_token`
				SET `access_token` = '" . $this->DataStore->real_escape_string($this->accessToken) . "',
					`access_token_secret` = '" . $this->DataStore->real_escape_string($this->accessTokenSecret) . "',
					`access_token_state` = '" . $this->DataStore->real_escape_string($this->accessTokenState) . "',
					`access_token_user_id` = '" . $this->DataStore->real_escape_string($this->accessTokenUserId) . "',
					`access_token_date` = '" . $this->DataStore->real_escape_string($this->accessTokenDate) . "',
					`access_token_consumer_key` = '" . $this->DataStore->real_escape_string($this->accessTokenConsumerKey) . "',
					`access_token_scope` = '" . $this->DataStore->real_escape_string($this->accessTokenScope) . "'
				WHERE `access_token_id` = '" . $this->DataStore->real_escape_string($this->accessTokenId) . "'";

		if (!$this->DataStore->query($sql)) {
			#TODO throw exception?
		}
	}

	protected function delete()
	{
		$sql = "DELETE FROM `oauth_provider_access_token`
				WHERE `access_token_id` = '" . $this->DataStore->real_escape_string($this->accessTokenId) . "'";

		if (!$this->DataStore->query($sql)) {
			#TODO throw exception?
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