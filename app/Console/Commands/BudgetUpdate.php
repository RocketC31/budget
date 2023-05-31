<?php

namespace App\Console\Commands;

use Exception;
use App\Console\Commands\BudgetCommand;

class BudgetUpdate extends BudgetCommand
{
    protected $signature = 'budget:update';
    protected $description = 'Update the application to the latest version';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        if (!$this->programExists('git')) {
            throw new Exception('Could not find Git');
        }

        // Fetch tags from repository
        $this->executeCommand(['git', 'fetch', '--tags']);

        $currentVersion = $this->executeCommand(['git', 'describe', '--tag', '--abbrev=0']);
        $rev = $this->executeCommand(['git', 'rev-list', '--tags', '--max-count=1']);
        $latestVersion = $this->executeCommand(['git', 'describe', '--tags', $rev]);

        $this->info('Currently running on ' . $currentVersion . ', latest version is ' . $latestVersion);

        if ($currentVersion === $latestVersion) {
            echo 'You\'ve already installed the latest version' . PHP_EOL;

            exit(0);
        }

        if (!$this->programExists('composer')) {
            throw new Exception('Could not find Composer, quitting update');
        }

        // Check out on latest version
        $this->executeCommand(['git', 'checkout', '-f', $latestVersion]);

        // Enable maintenance mode
        $this->executeCommand(['php', 'artisan', 'down'], true);

        $this->info('Installing Composer packages');
        $this->executeCommand(['composer', 'install', '--no-dev', '-o']);

        // Migrate database
        $this->executeCommand(['php', 'artisan', 'migrate', '--force'], true);

        try {
            $nodePackageManager = $this->option('node-package-manager');
        } catch (Exception $exception) {
            $nodePackageManager = null;
        }

        if (!$nodePackageManager) {
            $nodePackageManager = $this->choice('Which package manager would you like to use for Node.js?', [
                'npm',
                'yarn',
            ], 0);
        }

        if (!$this->programExists($nodePackageManager)) {
            $this->error('Could not find "' . $nodePackageManager . '", will not be able to compile front-end assets');
        } else {
            $this->info('Installing Node.js packages');
            $this->executeCommand([$nodePackageManager, 'install']);

            if ($nodePackageManager === 'npm') {
                $this->executeCommand(['git', 'restore', 'yarn.lock']);
            }

            $this->info('Compiling front-end assets');
            $this->executeCommand([$nodePackageManager, 'run', 'build']);
        }

        // Destroy existing sessions
        $this->executeCommand(['rm', storage_path(), '/framework/sessions/*']);

        // Disable maintenance mode
        $this->executeCommand(['php', 'artisan', 'up'], true);

        $this->info('Successfully updated to ' . $latestVersion . ', you should probably restart your queue workers as well'); // phpcs:ignore
    }
}
