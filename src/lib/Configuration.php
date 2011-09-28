<?php

require_once(__DIR__ . '/AutoLoader.php');
new AutoLoader();

class Configuration
{
	/**
	 * Returns an initialized DataStore Object. If the connection to the Datastore fails,
	 * a DataStoreConnectException is thrown.
	 *
	 * @static
	 * @throws DataStoreConnectException
	 * @return mysqli
	 */
	public static function getDataStore()
	{
		static $DataStore;

		if (!isset($DataStore)) {
			$DataStore = new mysqli('localhost', 'root', '', 'oauth');

			if ($DataStore->connect_error) {
				throw new DataStoreConnectException($DataStore->connect_error);
				exit;
			}
		}

		return $DataStore;
	}
}