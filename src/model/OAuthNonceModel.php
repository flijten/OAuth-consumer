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

class OAuthNonceModel extends ModelBase implements JsonSerializable
{
	/**
	 * @var string
	 */
	private $type					= "oauth_provider_nonce";
	/**
	 * @var string
	 */
	private $nonce 				= null;
	/**
	 * @var string
	 */
	private $nonceConsumerKey	= null;
	/**
	 * @var sting
	 */
	private $nonceDate			= null;

    public function jsonSerialize() {
        return [
            "type" => $this->type,
            "nonce" => $this->nonce,
            "nonce_consumer_key" => $this->nonceConsumerKey,
            "nonce_date" => $this->nonceDate
        ];
    }

// CRUD

	/**
	 * @static
	 * @param 	string $nonce
	 * @param 	$DataStore
	 * @return 	bool
	 */
	public static function nonceExists($nonce, $DataStore)
	{
		$result = $DataStore->view("dev_oauth", "getOAuthProviderNonceByNonce", array("stale" => false, "limit" => 1, "key" => $nonce, "inclusive_end" => true));

		return count($result["rows"]) > 0;
	}

	/**
	 * @throws DataStoreCreateException
	 * @return void
	 */
	public function create()
	{
		$result = $this->DataStore->add($this->nonce, json_encode($this));

		if (!$result) {
			throw new DataStoreCreateException("Couldn't save the nonce to the datastore");
		}
	}

	/**
	 * @throws DataStoreReadException
	 * @return array
	 */
	protected function read()
	{
		$result = $this->DataStore->view("dev_oauth", "getOAuthProviderNonceByNonce", array("stale" => false, limit => 1, "key" => $this->nonce, "inclusive_end" => true));

		if (!$result) {
			throw new DataStoreReadException("Couldn't read the nonce data from the datastore");
		}

		return $result;
	}

	/**
	 * @throws DataStoreUpdateException
	 * @return void
	 */
	protected function update()
	{
		$result = $this->DataStore->replace($this->nonce, json_encode($this));

		if (!$result) {
			throw new DataStoreUpdateException("Couldn't update the nonce to the datastore");
		}
	}

	/**
	 * @throws DataStoreDeleteException
	 * @return void
	 */
	public function delete()
	{
		$result = $this->DataStore->delete($this->nonce);

		if (!$result) {
			throw new DataStoreDeleteException("Couldn't delete the nonce from the datastore");
		}
	}

// Getters and setters

	/**
	 * @param string $nonce
	 */
	public function setId($nonce)
	{
		$this->nonce = $nonce;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->nonce;
	}

	/**
	 * @param string $nonceConsumerKey
	 */
	public function setNonceConsumerKey($nonceConsumerKey)
	{
		$this->nonceConsumerKey = $nonceConsumerKey;
	}

	/**
	 * @return string
	 */
	public function getNonceConsumerKey()
	{
		return $this->nonceConsumerKey;
	}

	/**
	 * @param \sting $nonceDate
	 */
	public function setNonceDate($nonceDate)
	{
		$this->nonceDate = $nonceDate;
	}

	/**
	 * @return \sting
	 */
	public function getNonceDate()
	{
		return $this->nonceDate;
	}
}
