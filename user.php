<?php
/**
 * @see https://secure.php.net/manual/en/function.crypt.php
 * @see https://secure.php.net/manual/en/ref.mcrypt.php
*/
namespace shgysk8zer0\Linux;
use \shgysk8zer0\Core_API as API;

final class User implements API\Interfaces\Magic_Methods
{
	use API\Traits\Magic_Methods;

	const MAGIC_PROPERTY = '_data';
	const RESTRICT_SETTING = true;

	private $_data = array(
		'user' => '',
		'pass' => '',
	);


	public function __construct($user, Passwd $pass = null)
	{
		if (! @filter_var("$user@example.com", \FILTER_VALIDATE_EMAIL)) {
			throw new \InvalidArgumentException(sprintf('"%s" does not appear to be a valid username.', $user));
		}
		$this->user = $user;
		$this->pass = "$pass";
	}

	public function __toString()
	{
		return json_encode($this->{self::MAGIC_PROPERTY}, JSON_PRETTY_PRINT);
	}
}
