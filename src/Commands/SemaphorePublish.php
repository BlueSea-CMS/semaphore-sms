<?php

namespace BlueSea\Semaphore\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\InputOption;

class SemaphorePublish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'semaphore:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes Config file and Migrations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(!$this->option('config') && !$this->option('migration'))
        {
            $this->publishConfig();
        }

        if($this->option('config'))
        {
            $this->publishConfig();
        }

        if($this->option('migration'))
        {
            $this->publishMigration();
        }
    }

    public function publishConfig()
    {
        if($this->option('force') && file_exists(config_path('semaphore.php')))
        {
            $this->info('Force publishing Semaphore config');
            if(unlink(config_path('semaphore.php')))
            {
                $this->info('Semaphore config will reset');
            }
        }

        if(!file_exists(config_path('semaphore.php')))
        {
            if($this->publish($this->resourcePath('config/semaphore.php'), config_path('semaphore.php')))
            {
                $this->info('Semaphore config published');
            } else {
                $this->error('Failed to publish Semaphore config');
            }
        } else {
            $this->error('Failed to publish Semaphore config');
        }
    }

    public function publishMigration()
    {
        $migrations = scandir($this->resourcePath('migrations/'));

        array_splice($migrations, array_search('.', $migrations), 1);

        array_splice($migrations, array_search('..', $migrations), 1);

        foreach($migrations as $migration)
        {
            $this->info('Creating ' . $migration . ' migration');
            if(
                $this->publish(
                    $this->resourcePath('migrations/' . $migration),
                    database_path('migrations/' . date('Y_m_d_His') . '_' . $migration)
                )
            ) {
                $this->info('Migration ' . $migration . ' created');
            } else {
                $this->error('Migration ' . $migration . ' failed');
            }
        }
    }

    /**
     * @param string $src
     * @param string $dest
     */
    public function publish($src, $dest)
    {
        return copy($src, $dest);
    }

    public function resourcePath($res)
    {
        return __DIR__ . '../../resources/' . $res;
    }

    public function resourceExists($res)
    {
        return file_exists($this->resourcePath($res));
    }

    public function getDefinition()
    {
        $definition = parent::getDefinition();
        $definition->addOption(new InputOption('config', 'c', InputOption::VALUE_NONE, 'Publishes config file only'));
        $definition->addOption(new InputOption('migration', 'm', InputOption::VALUE_NONE, 'Publishes migrations only'));
        $definition->addOption(new InputOption('force', 'f', InputOption::VALUE_NONE, 'Force Publish. Replace Existing files'));
        return $definition;
    }
}
