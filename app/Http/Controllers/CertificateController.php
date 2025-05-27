<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class CertificateController extends Controller
{
    public function download()
    {
        $user = Auth::user();

        $pdf = Pdf::loadView('certificate.pdf', ['user' => $user]);
        return $pdf->download('Сертифікат.pdf');
    }
}
