<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SSH;
use Illuminate\Http\Request; 
use App\Imports\MsisdnsImport;
use App\Services\NGBSSService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Exports\UsersExport;
use Collective\Remote\RemoteServiceProvider;
use File;
use App\Page;
use total_data;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Ixudra\Curl\Facades\Curl;

class cron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Run by scheduel';

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
        //
        // $HotBillController =  new HotBillController();
        // $HotBillController->getExchangeRate();
        $ch = curl_init("https://www.nbc.org.kh/english/economic_research/exchange_rate.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        $value = substr($content, strpos($content,'Official Exchange Rate : <font color="#FF3300">')+47, 4);
     

        $resultId = DB::table("exchangrate")
        ->insertGetId(
            [   
                "ExchangeRate" => $value,
                "Date" =>Carbon::now(),
            ]
        );
        curl_close($ch);
    }
}
