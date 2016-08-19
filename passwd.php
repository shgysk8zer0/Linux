<?php

namespace shgysk8zer0\Linux;

final class Passwd extends Algo
{
	private $_pass = '';
	private $_algo = 0;
	public function __construct($pass, $algo = self::SHA512)
	{
		if (is_string($algo)) {
			$this->getAlgoCode($algo);
		}
		$this->_pass = $pass;
		$this->_algo = $algo;
	}

	public function __toString()
	{
		$salt = "\${$this->_algo}\${$this->getSalt($this->_algo)}";
		return crypt($this->_pass, $salt);
	}
}
