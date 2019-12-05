<?php

namespace App\Http\Controllers;

use Catzilla\ZBarWrapper\ZBarWrapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Spatie\PdfToImage\Pdf;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BarCodeController extends Controller
{

    public function upload()
    {
        return view('barcode.upload');
    }

    public function file(Request $request)
    {

        if ($request->hasFile('pdfFile')) {

            $request->validate([
                'pdfFile' => 'mimetypes:application/pdf|max:10000',
            ]);

            $file = $request->file('pdfFile');

            // storage/app/public/images
            Storage::disk('pdfFiles')->putFileAs(null, $file, 'tickets.pdf');

            $pdf = new Pdf(Storage::disk('pdfFiles')->getAdapter()->getPathPrefix() . 'tickets.pdf');
            $pdf->saveAllPagesAsImages(Storage::disk('pdfFiles')->getAdapter()->getPathPrefix());

            unlink(Storage::disk('pdfFiles')->getAdapter()->getPathPrefix() . 'tickets.pdf');

            $manager = new ImageManager(['driver' => 'imagick']);
            $zbar = new ZBarWrapper();

            $codes = [];
            $codes['sum'] = 0;
            $codes['pages'] = $pdf->getNumberOfPages();

            for ($i = 1; $i <= $pdf->getNumberOfPages(); $i++) {
                $file = Storage::disk('pdfFiles')->getAdapter()->getPathPrefix() . $i . '.jpg';

                $manager->make($file)
                    ->rotate("-90")
                    ->crop(220, 100, 80)
                    ->save();

                try {
                    foreach ($zbar->decode($file) as $result) {
                        dd($result);
                        $value = intval(substr($result->value, -4, 3)) / 100;
                        $codes[] = $value;
                        $codes['sum'] += $value;
                    }
                } catch (ProcessFailedException $e) {
                    // keine Barcodes gefunden
                }
                unlink($file);
            }

            return view('barcode.result', compact('codes'));
        } else {
            return redirect()->back();
        }
    }
}
