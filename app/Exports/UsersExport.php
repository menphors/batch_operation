<?php

namespace App\Exports;

use App\Page;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Log;
// use Maatwebsite\Excel\Concerns\WithHeadings;
class UsersExport implements FromCollection
{
    // private $data = "";
    // public function __construct($data)
    // {
    //     $this->data=$data;
    // }
    // public function headings(): array {
    //     return [
    //        "email","name","phone_number","position"
    //     ];
    //   }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // return collect(Page::getUsers($collection));
        // return collect(Page::getUsers($this->data)); for use 
        // return collect(Page::getUsers());
        
        // return page::all();
        //return Page::getUsers($this->data);// Use this if you return data from Model without using toArray().
        //log::debug($this->data);
    }
}
