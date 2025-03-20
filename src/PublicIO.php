<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools;

use Composer\IO\ConsoleIO;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PublicIO extends ConsoleIO
{
	private function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
	{
		parent::__construct($input, $output, $helperSet);
	}
	
	public static function fromConsoleIO(ConsoleIO $io): self
	{
		return new self(
			$io->input,
			$io->output,
			$io->helperSet,
		);
	}
	
	public function getInput(): InputInterface
	{
		return $this->input;
	}
	
	public function getOutput(): OutputInterface
	{
		return $this->output;
	}
}