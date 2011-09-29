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

class OAuthConsumerModel extends ModelBase
{
	/**
	 * @var int
	 */
	private $consumerId;
	/**
	 * @var string
	 */
	private $consumerKey;
	/**
	 * @var string
	 */
	private $consumerSecret;
	/**
	 * @var int (timestamp)
	 */
	private $consumerCreateDate;


// CRUD functions

	/**
	 * @static
	 * @throws 	DataStoreReadException
	 * @param 	$consumerKey
	 * @param 	$DataStore
	 * @return 	OAuthConsumerModel
	 */
	public static function loadFromConsumerKey($consumerKey, $DataStore)
	{
		$OAuthConsumer = new OAuthConsumerModel($DataStore);

		$sql = "SELECT *
				FROM `oauth_consumer`
				WHERE `consumer_key` = '" . $DataStore->real_escape_string($consumerKey) . "'";

		$result = $DataStore->query($sql);

		if (!$result || $result->num_rows < 1) {
			throw new DataStoreReadException("Couldn't read the consumer data from the datastore");
		}

		$data 	= $result->fetch_assoc();
		$result->close();

		$OAuthConsumer->setId($data['consumer_id']);
		$OAuthConsumer->setConsumerKey($data['consumer_key']);
		$OAuthConsumer->setConsumerSecret($data['consumer_secret']);
		$OAuthConsumer->setConsumerCreateDate($data['consumer_create_date']);

		return $OAuthConsumer;
	}

	/**
	 * @throws DataStoreCreateException
	 * @return void
	 */
	protected function create()
	{
		$sql = "INSERT INTO `oauth_consumer`
				SET `consumer_key` = '" . $this->DataStore->real_escape_string($this->consumerKey) . "
					`consumer_secret` = '" . $this->DataStore->real_escape_string($this->consumerSecret) . "',
					`consumer_create_date` = '" . $this->DataStore->real_escape_string($this->consumerCreateDate) . "'";

		if ($this->DataStore->query($sql)) {
			$this->tokenId = $this->DataStore->insert_id;
		} else {
			throw new DataStoreCreateException("Couldn't save the consumer to the datastore");
		}
	}

	/**
	 * @throws DataStoreReadException
	 * @return
	 */
	protected function read()
	{
		$sql = "SELECT *
				FROM `oauth_consumer`
				WHERE `consumer_id` = '" . $this->DataStore->real_escape_string($this->consumerId) . "'";

		$result = $this->DataStore->query($sql);

		if (!$result) {
			throw new DataStoreReadException("Couldn't read the consumer data from the datastore");
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
		$sql = "UPDATE `oauth_consumer`
				SET `consumer_key` = '" . $this->DataStore->real_escape_string($this->consumerKey) . "
					`consumer_secret` = '" . $this->DataStore->real_escape_string($this->consumerSecret) . "',
					`consumer_create_date` = '" . $this->DataStore->real_escape_string($this->consumerCreateDate) . "
				WHERE `consumer_id` = '" . $this->DataStore->real_escape_string($this->consumerId) . "'";

		if (!$this->DataStore->query($sql)) {
			throw new DataStoreUpdateException("Couldn't update the consumer to the datastore");
		}
	}

	/**
	 * @throws DataStoreDeleteException
	 * @return void
	 */
	protected function delete()
	{
		$sql = "DELETE FROM `oauth_consumer`
				WHERE `consumer_id` = '" . $this->DataStore->real_escape_string($this->consumerId) . "'";

		if (!$this->DataStore->query($sql)) {
			throw new DataStoreDeleteException("Couldn't delete the consumer from the datastore");
		}
	}

// Getters and setters

	/**
	 * @param int (timestamp) $consumerCreateDate
	 */
	public function setConsumerCreateDate($consumerCreateDate)
	{
		$this->consumerCreateDate = $consumerCreateDate;
	}

	/**
	 * @return int (timestamp)
	 */
	public function getConsumerCreateDate()
	{
		return $this->consumerCreateDate;
	}

	/**
	 * @param int $consumerId
	 */
	public function setId($consumerId)
	{
		$this->consumerId = $consumerId;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->consumerId;
	}

	/**
	 * @param string $consumerKey
	 */
	public function setConsumerKey($consumerKey)
	{
		$this->consumerKey = $consumerKey;
	}

	/**
	 * @return string
	 */
	public function getConsumerKey()
	{
		return $this->consumerKey;
	}

	/**
	 * @param string $consumerSecret
	 */
	public function setConsumerSecret($consumerSecret)
	{
		$this->consumerSecret = $consumerSecret;
	}

	/**
	 * @return string
	 */
	public function getConsumerSecret()
	{
		return $this->consumerSecret;
	}
}


//`consumer_id` int(11) NOT NULL AUTO_INCREMENT,
//`consumer_key` varchar(40) NOT NULL,
//`consumer_secret` varchar(40) NOT NULL,
//`consumer_create_date` int(11) NOT NULL,