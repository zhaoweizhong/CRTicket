<?php

namespace App\Console\Commands;

use App\Models\Station;
use Illuminate\Console\Command;

class Pinyin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pinyin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Pinyin';

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
        $pinyin = app('pinyin');
        
        $stations = Station::all();

        foreach ($stations as $station) {
            $station->quanpin = str_replace('-', '', $pinyin->permalink($station->name));
            $station->jianpin = $pinyin->abbr($station->name);
            $station->save();
        }
    }
}
