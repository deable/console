<?php

declare(strict_types=1);

namespace Deable\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;


class ConsoleApplication extends Application
{

	protected function getDefaultInputDefinition(): InputDefinition
	{
		$definition = parent::getDefaultInputDefinition();
		$definition->addOption(new InputOption('debug-mode', 'debug', InputOption::VALUE_NONE, 'Enable debug mode in console'));

		return $definition;
	}

}
