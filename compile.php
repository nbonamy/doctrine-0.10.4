<?php
require_once('lib/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
Doctrine::compile('Doctrine.compiled.php');
