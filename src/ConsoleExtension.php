<?php

declare(strict_types=1);

namespace Deable\Console;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Nette\InvalidArgumentException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Command\Command;


class ConsoleExtension extends CompilerExtension
{
	/** @var bool */
	private $cliMode;

	public function __construct(bool $cliMode = false)
	{
		if (func_num_args() <= 0) {
			throw new InvalidArgumentException(sprintf('Provide CLI mode, e.q. %s(%%consoleMode%%).', self::class));
		}

		$this->cliMode = $cliMode;
	}

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'application' => Expect::string(ConsoleApplication::class),
			'url' => Expect::anyOf(Expect::string(), Expect::null()),
			'name' => Expect::string(),
			'version' => Expect::anyOf(Expect::string(), Expect::int(), Expect::float()),
			'locksDir' => Expect::string()
		]);
	}

	public function loadConfiguration(): void
	{
		if ($this->cliMode !== true) {
			return;
		}

		$builder = $this->getContainerBuilder();
		$application = $builder->addDefinition($this->prefix('application'))
			->setFactory($this->config->application);

		if ($this->config->name !== null) {
			$application->addSetup('setName', [$this->config->name]);
		}
		if ($this->config->version !== null) {
			$application->addSetup('setVersion', [(string) $this->config->version]);
		}
	}

	/**
	 * @throws ReflectionException
	 */
	public function beforeCompile(): void
	{
		if ($this->cliMode !== true) {
			return;
		}

		$builder = $this->getContainerBuilder();

		if ($this->config->url !== null && $builder->hasDefinition('http.request')) {
			/** @var ServiceDefinition $httpRequest */
			$httpRequest = $builder->getDefinition('http.request');
			$httpRequest->setFactory(Request::class, [new Statement(UrlScript::class, [$this->config->url])]);
		}

		/** @var ServiceDefinition $application */
		$application = $builder->getDefinition($this->prefix('application'));
		/** @var ServiceDefinition[] $commands */
		$commands = $builder->findByType(Command::class);
		foreach ($commands as $command) {
			$reflection = new ReflectionClass($command->getType());
			if ($reflection->isSubclassOf(SynchronizedCommand::class)) {
				$command->addSetup('setLocksDir', [$this->config->locksDir]);
			}

			$application->addSetup('add', [$command]);
		}
	}

}
