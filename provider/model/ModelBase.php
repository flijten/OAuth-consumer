<?php


abstract class ModelBase
{
	/**
	 * Should be some sort of DB object. This is field exists so it is easy to start using a different
	 * storage method than MySQL. Simply overwriting the CRUD functions of this object and injecting
	 * the object with the correct dataStore object should suffice.
	 *
	 * @var MySQLi|mixed default is a MySQLi object
	 */
	protected $DataStore			= null;

	/**
	 * Instantiate object and set $DataStore field
	 *
	 * @param $DataStore
	 */
	public function __construct($DataStore)
	{
		$this->DataStore = $DataStore;
	}

	/**
	 * Writes the values of the class fields to the dataStore. If this->tokenId is already set, an
	 * update is performed, else a new record is created.
	 *
	 * @return void
	 */
	public function save()
	{
		#TODO return values? catch or rethrow exceptions?
		#TODO tokenId isn't universal so save isn't as well.
		$id = $this->getId();
		if (empty($id)) {
			$this->create();
		} else {
			$this->update();
		}
	}

	abstract protected function getId();

	abstract protected function create();

	abstract protected function read();

	abstract protected function update();

	abstract protected function delete();
}