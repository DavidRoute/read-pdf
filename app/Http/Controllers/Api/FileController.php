<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;

class FileController extends Controller
{
    public function store(Request $request) 
    {
        $request->validate([
            'file' => ['required', 'mimes:pdf', 'max:2048']
        ]);

        $file = $request->file('file');
        $file->store('pdf');

        $pdfParser = new Parser();
        $pdf = $pdfParser->parseFile($file->path());
        $content = $pdf->getText();

        return response()->json(['data' => $content]);

        preg_match('/\\n([\w\s()\d]+)\\tCONFIDENTIAL/m', $content, $company);

        $agiPdfPattern = '/([0-9]+\/[0-9]+\/[0-9]+)\s+?\\n-\s+\\n([0-9]+)\s+?[0-9]+\/[0-9]+\/[0-9]+\s+?([\w]+)\s+?[\d\.]+\s+?[\d\.]+\s+?([0-9]+\/[0-9]+\/[0-9]+)/m';

        if (count($company)) {
            preg_match('/(Policy Number:)\\t(\d+)\\t/m', $content, $policy);
            preg_match('/(Plan Type:)\\t([\w\s()\d]+)\\t/m', $content, $product);
            preg_match('/\\n([\w\s()\d]+)\\tCONFIDENTIAL/m', $content, $company);
            preg_match(
                '/End Date\\t[\w\s()*\\t\d\.]+\\t([\d]+-[\w]+-[\d]+)\\t([\d]+-[\w]+-[\d]+)\\t/m', $content, $date
            );
            $data = [
                'policy_number' => isset($policy[2]) ? $policy[2] : null,
                'product_name'  => isset($product[2]) ? $product[2] : null,
                'company_name'  => isset($company[1]) ? $company[1] : null,
                'start_date'    => isset($date[1]) ? $date[1] : null,
                'end_date'      => isset($date[2]) ? $date[2] : null
            ];
        } 
        elseif (preg_match($agiPdfPattern, $content)) {
            preg_match($agiPdfPattern, $content, $matches);
            preg_match('/Accident\s+?&\s+?Health\s+?\\n([\w\s]+)/m', $content, $company);

            $data = [
                'policy_number' => $matches[2] ?? null,
                'product_name'  => $matches[3] ?? null,
                'company_name'  => $company[1] ?? null,
                'start_date'    => $matches[1] ?? null,
                'end_date'      => $matches[4] ?? null
            ];
        }
        else {
            preg_match('/\\n([\w-]+)\\n[\w]+\\nInvoice number/m', $content, $policy);
            preg_match('/Invoice number\s+:\\n:\\n:\s?([A-Za-z0-9 ]+)\\t/m', $content, $product);
            preg_match('/Date:\\t\\n([a-zA-Z ]+)/m', $content, $company);
            preg_match('/Product\s?([0-9]+\/[0-9]+\/[0-9]+)\\n([0-9]+\/[0-9]+\/[0-9]+)/m', $content, $date);

            $data = [
                'policy_number' => isset($policy[1]) ? $policy[1] : null,
                'product_name'  => isset($product[1]) ? $product[1] : null,
                'company_name'  => isset($company[1]) ? $company[1] : null,
                'start_date'    => isset($date[1]) ? $date[1] : null,
                'end_date'      => isset($date[2]) ? $date[2] : null
            ];
        }

        return response()->json(compact('data'));
    }

}
