<?php


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

	public static function loadFromConsumerKey($consumerKey, $DataStore)
	{
		$OAuthConsumer = new OAuthConsumerModel($DataStore);

		$sql = "SELECT *
				FROM `oauth_consumer`
				WHERE `consumer_key` = '" . $DataStore->real_escape_string($consumerKey) . "'";

		$result = $DataStore->query($sql);

		if (!$result || $result->num_rows < 1) {
			return false;
		}

		$data 	= $result->fetch_assoc();
		$result->close();

		$OAuthConsumer->setId($data['consumer_id']);
		$OAuthConsumer->setConsumerKey($data['consumer_key']);
		$OAuthConsumer->setConsumerSecret($data['consumer_secret']);
		$OAuthConsumer->setConsumerCreateDate($data['consumer_create_date']);

		return $OAuthConsumer;
	}

	protected function create()
	{
		$sql = "INSERT INTO `oauth_consumer`
				SET `consumer_key` = '" . $this->DataStore->real_escape_string($this->consumerKey) . "
					`consumer_secret` = '" . $this->DataStore->real_escape_string($this->consumerSecret) . "',
					`consumer_create_date` = '" . $this->DataStore->real_escape_string($this->consumerCreateDate) . "'";

		if ($this->DataStore->query($sql)) {
			$this->tokenId = $this->DataStore->insert_id;
		} else {
			#TODO throw exception?
		}
	}

	protected function read()
	{
		$sql = "SELECT *
				FROM `oauth_consumer`
				WHERE `consumer_id` = '" . $this->DataStore->real_escape_string($this->consumerId) . "'";

		$result = $this->DataStore->query($sql);
		$data 	= $result->fetch_assoc();
		$result->close();

		return $data;
	}

	protected function update()
	{
		$sql = "UPDATE `oauth_consumer`
				SET `consumer_key` = '" . $this->DataStore->real_escape_string($this->consumerKey) . "
					`consumer_secret` = '" . $this->DataStore->real_escape_string($this->consumerSecret) . "',
					`consumer_create_date` = '" . $this->DataStore->real_escape_string($this->consumerCreateDate) . "
				WHERE `consumer_id` = '" . $this->DataStore->real_escape_string($this->consumerId) . "'";

		if (!$this->DataStore->query($sql)) {
			#TODO throw exception?
		}
	}

	protected function delete()
	{
		$sql = "DELETE FROM `oauth_consumer`
				WHERE `consumer_id` = '" . $this->DataStore->real_escape_string($this->consumerId) . "'";

		if (!$this->DataStore->query($sql)) {
			#TODO throw exception?
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