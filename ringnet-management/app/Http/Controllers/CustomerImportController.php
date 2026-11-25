<?php

namespace App\Http\Controllers;

use App\Imports\CustomersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerImportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        Excel::import(new CustomersImport, $request->file('file'));

        return redirect()
            ->route('customers.index')
            ->with('success', 'Import data pelanggan berhasil.');
    }
}
