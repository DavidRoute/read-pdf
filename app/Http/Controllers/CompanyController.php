<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\ProcessPdf;

class CompanyController extends Controller
{
    public function store() 
    {

        \DB::table('companies')->orderBy('id')->chunk(5000, function ($companies) {
            ProcessPdf::dispatch($companies);

            return false;
        });

        

        return 'Ok';
        // ProcessPdf::dispatch($companies);
    }
}
