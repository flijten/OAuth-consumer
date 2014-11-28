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

include_once("ModelBase.php");

class OAuthRequestTokenModel extends ModelBase implements JsonSerializable
{

//OAuth access token specific fields

	/**
	 * @var string
	 */
	private $type					= "oauth_provider_request_token";
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

    public function jsonSerialize() {
        return [
            "type" => $this->type,
            "request_token_id" => $this->tokenId,
            "request_token" => $this->token,
            "request_token_secret" => $this->tokenSecret,
            "request_token_verification_code" => $this->tokenVerificationCode,
            "request_token_user_id" => $this->tokenUserId,
            "request_token_date" => $this->tokenDate,
            "request_token_consumer_key" => $this->tokenConsumerKey,
            "request_token_callback" => $this->tokenCallback,
            "request_token_scope" => $this->tokenScope
        ];
    }


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
		$result = $DataStore->view("dev_oauth", "getOAuthProviderRequestTokenByRequestToken", array("stale" => false, "limit" => 1, "key" => $token , "inclusive_end" => true));

		if (!$result || count($result["rows"]) < 1) {
			throw new DataStoreReadException("Couldn't read the request token data from the datastore");
		}

		$RequestToken = new OAuthRequestTokenModel($DataStore);
		$RequestToken->tokenId = $result["rows"][0]["value"]['request_token_id'];
		$RequestToken->token = $result["rows"][0]["value"]['request_token'];
		$RequestToken->tokenSecret = $result["rows"][0]["value"]['request_token_secret'];
		$RequestToken->tokenVerificationCode = $result["rows"][0]["value"]['request_token_verification_code'];
		$RequestToken->tokenUserId = $result["rows"][0]["value"]['request_token_user_id'];
		$RequestToken->tokenDate = $result["rows"][0]["value"]['request_token_date'];
		$RequestToken->tokenConsumerKey = $result["rows"][0]["value"]['request_token_consumer_key'];
		$RequestToken->tokenCallback = $result["rows"][0]["value"]['request_token_callback'];
		$RequestToken->tokenScope = $result["rows"][0]["value"]['request_token_scope'];

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
		$this->tokenId = uniqid();
		$user_id = $this->tokenUserId ? $this->tokenUserId : 'NULL';

		$result = $this->DataStore->add($this->tokenId, json_encode($this));

		if (!$result) {
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
		$result = $this->DataStore->view("dev_oauth", "getOAuthProviderRequestTokenByRequestTokenId", array("stale" => false, limit => 1, "key" => $this->tokenId, "inclusive_end" => true));

		if (!$result) {
			throw new DataStoreReadException("Couldn't read the nonce data from the datastore");
		}

		return $result;
	}

	/**
	 * Updates the row in the datastore for this OAuth access token.
	 *
	 * @throws DataStoreReadException
	 * @return void
	 */
	protected function update()
	{
		$result = $this->DataStore->replace($this->tokenId, json_encode($this));

		if (!$result) {
			throw new DataStoreUpdateException("Couldn't update the request token to the datastore");
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
		$result = $this->DataStore->delete($this->tokenId);

		if (!$result) {
			throw new DataStoreDeleteException("Couldn't delete the request token data in the datastore");
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
