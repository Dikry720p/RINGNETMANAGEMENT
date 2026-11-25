<?php

namespace App\Http\Controllers;

use App\Imports\PackagesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PackageImportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        Excel::import(new PackagesImport, $request->file('file'));

        return redirect()
            ->route('packages.index')
            ->with('success', 'Import jenis paket berhasil.');
    }
}
