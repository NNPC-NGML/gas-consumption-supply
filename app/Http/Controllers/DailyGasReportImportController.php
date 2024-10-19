<?php

namespace App\Http\Controllers;

use App\Imports\DailyGasReportImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class DailyGasReportImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        try {
            $import = new DailyGasReportImport();

            Excel::import($import, $request->file('file'));

            Log::info('CSV file imported successfully.');

            $logData = $import->getLogData();

            return response()->json([
                'message' => 'CSV file imported successfully.',
                'data' => $logData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error during CSV import: ' . $e->getMessage());
            return response()->json(['message' => 'Error during import: ' . $e->getMessage()], 500);
        }
    }
}
