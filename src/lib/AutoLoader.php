<?php

class AutoLoader
{
	public function __construct()
	{
		spl_autoload_register(array($this, 'autoLoadOAuthProviderClasses'));
	}

	public function autoLoadOAuthProviderClasses($className)
	{

		if (preg_match('/Exception$/', $className)) {
			$this->loadExceptionClass($className);
		} else if (preg_match('/Model/', $className)) {
			$this->loadModelClass($className);
		} else {
			$this->loadClass($className);
		}
	}

	private function loadClass($className)
	{
		$index = array(
			'Configuration'			=> 'lib/Configuration.php',
			'OAuthProviderWrapper'	=> 'OAuthProviderWrapper.php',
		);

		$classPath = '';

		if (isset($index[$className])) {
			$classPath = __DIR__ . '/../' . $index[$className];
		}

		if (file_exists($classPath)) {
			require_once $classPath;
		}

	}

	/**
	 * Loads model class files
	 *
	 * @param 	$className
	 */
	private function loadModelClass($className)
	{
		$filePath = __DIR__ . '/../model/' . $className . '.php';

		if (file_exists($filePath)) {
			require_once $filePath;
		}
	}

	/**
	 * @param 	$className
	 */
	private function loadExceptionClass($className)
	{

		$index = array(
			'ProviderException'			=> 'ProviderException.php',
			'DataStoreConnectException'	=> 'datastore/DataStoreConnectException.php',
		);

		$classPath = '';

		if (isset($index[$className])) {
			$classPath = __DIR__ . '/../exception/' . $index[$className];
		}

		if (file_exists($classPath)) {
			require_once $classPath;
		}
	}
}

