<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Exception;

class BudgetCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'budget:info';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Please use `budget:install` or `budget:update`');

        $version = file_exists('version.txt') ? $this->executeCommand(['cat', 'version.txt']) : '';
        $this->info('Current version: ' . $version);
    }

    public function executeCommand($command, $showOutput = false): string
    {
        $process = new Process($command);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if ($showOutput === true) {
            $this->line(ltrim(rtrim($process->getOutput(), PHP_EOL), PHP_EOL));
        }

        return rtrim($process->getOutput(), PHP_EOL);
    }

    public function programExists(string $program): bool
    {
        try {
            $this->executeCommand(['which', $program]);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
