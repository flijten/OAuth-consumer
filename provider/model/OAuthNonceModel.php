<?php


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

	public static function nonceExists($nonce, $DataStore)
	{
		$sql = "SELECT 1
				FROM `oauth_provider_nonce`
				WHERE `nonce` = 'S" . $DataStore->real_escape_string($nonce) . "'";

		$result = $DataStore->query($sql);

		return $result->num_rows > 0;
	}

	protected function create()
	{
		$sql = "INSERT INTO `oauth_provider_nonce`
				SET `nonce` = '" . $this->DataStore->real_escape_string($this->nonce) . "',
					`nonce_consumer_key` = '" . $this->DataStore->real_escape_string($this->nonceConsumerKey) . "',
					`nonce_date` = '" . $this->DataStore->real_escape_string($this->nonceDate) . "'";

		if (!$this->DataStore->query($sql)) {
			#TODO throw exception?
		}
	}

	protected function read()
	{
		$sql = "SELECT *
				FROM `oauth_provider_nonce`
				WHERE `nonce` = '" . $this->DataStore->real_escape_string($this->nonce) . "'";

		$result = $this->DataStore->query($sql);
		$data 	= $result->fetch_assoc();
		$result->close();

		return $data;
	}

	protected function update()
	{
		$sql = "UPDATE `oauth_provider_nonce`
				SET `nonce_consumer_key` = '" . $this->DataStore->real_escape_string($this->nonceConsumerKey) . "',
					`nonce_date` = '" . $this->DataStore->real_escape_string($this->nonceDate) . "'
				WHERE `nonce` = '" . $this->DataStore->real_escape_string($this->nonce) . "'";

		if (!$this->DataStore->query($sql)) {
			#TODO throw exception?
		}
	}

	protected function delete()
	{
		$sql = "DELETE FROM `oauth_provider_nonce`
				WHERE `nonce` = '" . $this->DataStore->real_escape_string($this->nonce) . "'";

		if (!$this->DataStore->query($sql)) {
			#TODO throw exception?
		}
	}

// Getters and setters

	/**
	 * @param string $nonce
	 */
	public function setNonce($nonce)
	{
		$this->nonce = $nonce;
	}

	/**
	 * @return string
	 */
	public function getNonce()
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
