<?php
namespace shgysk8zer0\Linux;

class Management
{
	const EXPIRES    = false;
	const USERS_DIR  = '/home';
	const ACTIONS = '_actions';

	private $_actions = array(
		'makeUser'   => array(),
		'changePass' => array(),
		'deleteUser' => array(),
		'lockUser'   => array(),
		'unlockUser' => array(),
	);

	public function __construct()
	{
		if (! $this->_isCLI() or ! $this->_isRoot()) {
			http_response_code(400);
			trigger_error('This is only available via a cron job.');
			exit(1);
		}
	}

	final public function __isset($action)
	{
		return array_key_exists($action, $this->{self::ACTIONS});
	}

	final public function __set($action, Users $users)
	{
		if ($this->__isset($action)) {
			$this->{self::ACTIONS}[$action] = $users;
		} else {
			trigger_error(sprintf('Undefined action: "%s"', $action));
		}
	}

	final public function __get($action)
	{
		if ($this->__isset($action)) {
			return $this->{self::ACTIONS}[$action];
		} else {
			return array();
		}
	}

	final public function __invoke(Array $actions = array())
	{
		if (! is_array($actions) or empty($actions)) {
			$actions = array_keys($this->{self::ACTIONS});
		} else {
			$actions = array_filter($actions, [$this, '__isset']);
		}

		print_r($actions);

		foreach($actions as $action) {
			$users = $this->__get($action);
			if ($users instanceof Users) {
				echo "Now running {$action}." . PHP_EOL;
				foreach ($users as $user) {
					$this->{"_${action}"}($user);
				}
			}

			//array_map([$this, "_{$action}"], $users);
		}
	}

	private function _userExists(User $user)
	{
		return is_dir("/home/{$user->user}");
	}

	private function _makeUser(User $user)
	{
		if (! $this->_userExists($user)) {
			$cmd = new Shell('useradd --shell /bin/bash --create-home --password pwd username');
			$cmd->pwd = $user->pass;
			$cmd->username = $user->user;
			return $cmd();
		} else {
			trigger_error(sprintf('User "%s" already exists', $user->user));
			return false;
		}
	}

	private function _changePass(User $user)
	{
		if ($this->_userExists($user)) {
			$cmd = new Shell('usermod --password pwd username');
			$cmd->username = $user->user;
			$cmd->pwd = $user->pass;
			return $cmd();
		} else {
			trigger_error(sprintf('No user exists by the name of "%s"', $user->user));
			return false;
		}
	}

	private function _deleteUser(User $user)
	{
		if ($this->_userExists($user)) {
			$cmd = new Shell('userdel -r username');
			$cmd->username = $user->user;
			return $cmd();
		} else {
			trigger_error(sprintf('No user exists by the name of "%s"', $user->user));
			return false;
		}
	}

	private function _lockUser(User $user)
	{
		if ($this->_userExists($user)) {
			$cmd = new Shell('usermod --lock username');
			$cmd->username = $user->user;
			return $cmd();
		} else {
			trigger_error(sprintf('No user exists by the name of "%s"', $user->user));
			return false;
		}
	}

	private function _unlockUser(User $user)
	{
		if ($this->_userExists($user)) {
			$cmd = new Shell('usermod --unlock username');
			$cmd->username = $user->user;
			return $cmd();
		} else {
			trigger_error(sprintf('No user exists by the name of "%s"', $user->user));
			return false;
		}
	}

	private function _getUsers()
	{
		return new Users();
	}

	private function _isRoot()
	{
		return trim(`whoami`) === 'root';
	}

	private function _isCLI()
	{
		return in_array(PHP_SAPI, array('cli'));
	}
}

