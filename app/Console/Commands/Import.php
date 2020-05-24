<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import';

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
     * @return mixed
     */
    public function handle()
    {
        $trains = json_decode('');
        
        for ($i=0; $i < count($trains); $i++) { 
            User::create([
                'username'=>$trains[$i]['ddd'],
                'password'=>''
                ]);
        }
    }
}
