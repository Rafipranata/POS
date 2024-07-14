<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\PDF;

class PdfController extends Controller
{
    public function generatePdf($id)
    {
        // Cari invoice berdasarkan ID beserta relasi Invoice Products
        $invoice = Invoice::with('invoiceProducts')->findOrFail($id);

        // Buat instance DomPDF wrapper
        $pdf = app('dompdf.wrapper');

        // Load view dengan data invoice
        $pdf->loadView('pdf', ['invoice' => $invoice]);

        // Nama file PDF
        $fileName = 'invoice-' . $invoice->id;

        // Mengirimkan respons PDF untuk diunduh
        return $pdf->stream($fileName . '.pdf');
    }
}
