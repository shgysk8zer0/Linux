<?php
namespace shgysk8zer0\Linux;

set_include_path(dirname(dirname(__DIR__)));
spl_autoload_register('spl_autoload');

$mg = new Management();
$mg->makeUser = new Users();
$mg();
