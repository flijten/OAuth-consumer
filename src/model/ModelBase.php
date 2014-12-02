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
		$id = $this->getId();
		if (empty($id)) {
			return $this->create();
		} else {
			return $this->update();
		}
	}

	abstract protected function getId();

	abstract protected function create();

	abstract protected function read();

	abstract protected function update();

	abstract public function delete();
}