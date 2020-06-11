<?php

require_once __DIR__.'/../bootstrap.php';

// Register the EntityManager to the console
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
