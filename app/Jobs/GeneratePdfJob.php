<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companies;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($companies)
    {
        $this->companies = $companies;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pdf = PDF::loadView('pdf.invoice', ['companies' => $this->companies]);
        $path = 'tmp-pdf/'.time().\Str::random(6).'.pdf';

        \Storage::disk('public')->put($path, $pdf->output());
    }
}
