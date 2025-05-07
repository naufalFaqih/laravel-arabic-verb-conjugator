<?php
// filepath: c:\laragon\www\latihanLaravel11\app\Http\Controllers\VerbController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerbController extends Controller
{
    public function search(Request $request)
    {
        $verb = $request->input('verb');
        // Logika pencarian kata kerja di sini
        return view('result', ['verb' => $verb]);
    }
}