<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MergeAndStorePdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:merge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $pdf = new \Jurosh\PDFMerge\PDFMerger;

        foreach (glob(storage_path('app/public/tmp-pdf/*')) as $filepath) {
            $pdf->addPDF($filepath, 'all');
        }

        \Storage::disk('public')->makeDirectory('company-pdf');
        $pdf->merge('file', storage_path('app/public/company-pdf/company.pdf'), 'P');

        \Storage::disk('public')->deleteDirectory('tmp-pdf');

        \Storage::disk('s3')->put(
            'final.pdf', 
            file_get_contents(storage_path('app/public/company-pdf/company.pdf'))
        );
    }
}
