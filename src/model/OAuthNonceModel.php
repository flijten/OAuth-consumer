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

class OAuthNonceModel extends ModelBase
{
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

// CRUD

	/**
	 * @static
	 * @param 	string $nonce
	 * @param 	$DataStore
	 * @return 	bool
	 */
	public static function nonceExists($nonce, $DataStore)
	{
		$sql = "SELECT 1
				FROM `oauth_provider_nonce`
				WHERE `nonce` = 'S" . $DataStore->real_escape_string($nonce) . "'";

		$result = $DataStore->query($sql);

		return $result->num_rows > 0;
	}

	/**
	 * @throws DataStoreCreateException
	 * @return void
	 */
	public function create()
	{
		$sql = "INSERT INTO `oauth_provider_nonce`
				SET `nonce` = '" . $this->DataStore->real_escape_string($this->nonce) . "',
					`nonce_consumer_key` = '" . $this->DataStore->real_escape_string($this->nonceConsumerKey) . "',
					`nonce_date` = '" . $this->DataStore->real_escape_string($this->nonceDate) . "'";

		if (!$this->DataStore->query($sql)) {
			throw new DataStoreCreateException("Couldn't save the nonce to the datastore");
		}
	}

	/**
	 * @throws DataStoreReadException
	 * @return array
	 */
	protected function read()
	{
		$sql = "SELECT *
				FROM `oauth_provider_nonce`
				WHERE `nonce` = '" . $this->DataStore->real_escape_string($this->nonce) . "'";

		$result = $this->DataStore->query($sql);

		if (!$result) {
			throw new DataStoreReadException("Couldn't read the nonce data from the datastore");
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
		$sql = "UPDATE `oauth_provider_nonce`
				SET `nonce_consumer_key` = '" . $this->DataStore->real_escape_string($this->nonceConsumerKey) . "',
					`nonce_date` = '" . $this->DataStore->real_escape_string($this->nonceDate) . "'
				WHERE `nonce` = '" . $this->DataStore->real_escape_string($this->nonce) . "'";

		if (!$this->DataStore->query($sql)) {
			throw new DataStoreUpdateException("Couldn't update the nonce to the datastore");
		}
	}

	/**
	 * @throws DataStoreDeleteException
	 * @return void
	 */
	public function delete()
	{
		$sql = "DELETE FROM `oauth_provider_nonce`
				WHERE `nonce` = '" . $this->DataStore->real_escape_string($this->nonce) . "'";

		if (!$this->DataStore->query($sql)) {
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
