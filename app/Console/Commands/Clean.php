<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Artisan;

class Clean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean / Optimize the App';

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
        try {
            // logic for running laravel's artisan commands.
            Artisan::call('down');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('route:cache');
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            Artisan::call('view:clear');
            Artisan::call('view:cache');
            Artisan::call('optimize');
            Artisan::call('up');
            // logic for running composer's commands.
            // app()->make(Composer::class)->run(['dump-autoload']);
            // app()->make(Composer::class)->run(['update']);
            // app()->make(Composer::class)->run(['dump-autoload']);
        } catch (\Throwable $th) {
            // throw $th;
            dd($th->getMessage());
        }

        return "App has been cleaned";
    }
}
