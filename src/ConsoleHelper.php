<?php

declare(strict_types=1);

namespace Deable\Console;

use Nette;
use Nette\Bootstrap\Configurator;
use Symfony\Component\Console\Input\ArgvInput;


final class ConsoleHelper
{
	use Nette\StaticClass;

	private const DEBUG_OPTIONS = [
		'--debug-mode=yes',
		'--debug-mode=on',
		'--debug-mode=true',
		'--debug-mode=1',
		'--debug-mode',
		'--debug',
	];

	public static function setupMode(Configurator $configurator, callable $setupFunction = null): void
	{
		if (PHP_SAPI === 'cli' && self::isConsoleDebug()) {
			$configurator->setDebugMode(true);
		} elseif ($setupFunction !== null) {
			$setupFunction();
		}
	}

	private static function isConsoleDebug(): bool
	{
		$argv = new ArgvInput();

		return $argv->hasParameterOption(self::DEBUG_OPTIONS);
	}

}
