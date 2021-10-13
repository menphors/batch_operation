<?php

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use App\Services\HotBill;
use SSH;
use phpseclib\Net\SSH2;
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
use DOMDocument;
use DOMXPath;
use DOMNode;
use SimpleXMLElement;
use Simplexml_load_file;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Ixudra\Curl\Facades\Curl;
// use Illuminate\Http\Request;


//=============SFTP =======================
class getMutipleDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */

    var $data = null;
  
    public function __construct($data)
    {

        $this->data = $data;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new HotBill())->HotBill_Operatrion($this->data);

    }

    
}
