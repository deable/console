<?php

declare(strict_types=1);

namespace Deable\Console;

use Exception;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


abstract class SynchronizedCommand extends Command
{
	/** @var string */
	private $locksDir;

	public function setLocksDir(string $locksDir): void
	{
		$this->locksDir = $locksDir;
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return int
	 * @throws Exception
	 * @throws ExceptionInterface
	 */
	final public function run(InputInterface $input, OutputInterface $output): int
	{
		if ($this->lock()) {
			return parent::run($input, $output);
		}

		$output->writeln(sprintf("<info>Task '%s' already runnning</info>", $this->getName()));
		return 1;
	}

	private function lock(): bool
	{
		static $lock; // static for lock until the process end

		FileSystem::createDir($this->locksDir);
		$path = sprintf('%s/cron-%s.lock', $this->locksDir, md5($this->getName()));
		$lock = fopen($path, 'w+b');

		return $lock !== FALSE && flock($lock, LOCK_EX | LOCK_NB);
	}

}
