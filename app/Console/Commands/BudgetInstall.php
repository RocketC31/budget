<?php

namespace App\Console\Commands;

use App\Console\Commands\BudgetCommand;
use Exception;

class BudgetInstall extends BudgetCommand
{
    protected $signature = 'budget:install {--node-package-manager=}';
    protected $description = 'Runs most of the commands needed to make Budget work';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Installing Composer packages');
        $this->executeCommand(['composer', 'install', '--no-dev', '-o']);

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

        if (!file_exists('.env')) {
            $this->info('Copying .env.example to .env');
            $this->executeCommand(['cp', '.env.example', '.env']);
        }

        if (env('APP_KEY') === '' || env('APP_KEY') === null) {
            $this->info('Generating key');
            $this->executeCommand(['php', 'artisan', 'key:generate']);
        }

        if (!file_exists('public/storage')) {
            $this->info('Creating symbolic link from public/storage to storage/app/public');
            $this->executeCommand(['php', 'artisan', 'storage:link']);
        }

        $this->info('Done!');
    }
}
