#!/usr/bin/env php
<?php

namespace AwesomeProject;

use Symfony\Component\VarDumper\VarDumper;

require_once 'vendor/autoload.php';

VarDumper::setHandler();

$app = new AwesomeProjectApplication();

$app->run();
