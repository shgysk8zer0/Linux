<?php

namespace shgysk8zer0\Linux;
use \shgysk8zer0\Core_API as API;

final class Shell
{
	use API\Traits\Magic_Methods;

	const MAGIC_PROPERTY = '_args';

	private $_args = array();

	private $_cmd = '';

	public function __construct($cmd)
	{
		if (is_string($cmd)) {
			$this->_cmd = escapeshellcmd($cmd);
		} else {
			throw new \InvalidArgumentException(sprintf('%s expects $cmd to be a string.', __FUNCTION__));
		}
	}

	public function __toString()
	{
		$keys = array_keys($this->{self::MAGIC_PROPERTY});
		$values = array_values($this->{self::MAGIC_PROPERTY});
		$values = array_map('escapeshellarg', $values);
		return str_replace($keys, $values, $this->_cmd);
	}

	public function __invoke(Array $args = array())
	{
		if (!empty($args)) {
			array_map([$this, '__set'], array_keys($args), array_values($args));
		}

		return `$this`;
	}
}
