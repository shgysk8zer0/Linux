<?php

namespace shgysk8zer0\Linux;

use \shgysk8zer0\Core_API as API;

final class Users implements API\Interfaces\ArrayMethods, \Iterator
{
	use API\Traits\Magic\Iterator;
	use API\Traits\ArrayMethods;

	const OUT_FILE = 'users.json';
	const MAGIC_PROPERTY = '_users';

	public static $out = self::OUT_FILE;
	private $_users = array();

	public function __construct(Array $users = array())
	{
		$this->_importUsers();
		array_map([$this, 'addUser'], $users);
	}

	public function __set($user, $pass)
	{
		$this->addUser(new User($user, new Passwd($pass)));
	}

	public function __toString()
	{
		return json_encode(array_reduce($this->_users, [$this, '_reduceUsers'], []));
	}

	public function addUser(User $user)
	{
		array_push($this->_users, $user);
	}

	private function _reduceUsers(Array $users = array(), User $user)
	{
		$tmp = new \stdClass();
		$tmp->user = $user->user;
		$tmp->pass = $user->pass;
		array_push($users, $tmp);
		return $users;
	}

	private function _importUsers($from = self::OUT_FILE)
	{
		if (file_exists(static::$out)) {
			$users = json_decode(file_get_contents(static::$out));
			$tmp = static::createFromArray($users);
			foreach ($tmp as $user) {
				$this->addUser($user);
			}

		} else {
			trigger_error("File: '{$from}' not found.");
		}
	}

	public static function import($from = null)
	{
		if (! is_string($from)) {
			$from = static::$out;
		}
		$tmp = new self();
		$tmp->_importUsers($from);
		return $tmp;
	}

	public static function createFromArray(Array $users)
	{
		$tmp = new self();
		foreach($users as $user) {
			if (is_object($user) and isset($user->user, $user->pass)) {
				foreach($users as $user) {
					$tmp = new User($user->user);
					$tmp->pass = $user->pass;
					array_push($this->_users, $tmp);
				}
			}
		}
		return $tmp;
	}
}
