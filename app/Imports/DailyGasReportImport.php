<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DailyGasReportImport implements ToModel, WithHeadingRow
{
    protected $offtakersNames = [
        ['name' => 'PARAS CAPTIVE'],
        ['name' => 'PARAS EMBEDDED'],
        ['name' => 'TOWER POWER'],
        ['name' => 'QUANTUM STEELS'],
        ['name' => 'NIGER BISCUIT'],
        ['name' => 'SUNFLAG STEEL'],
        ['name' => 'GREEN FUEL'],
        ['name' => 'SUNFLAG STEEL (SHAGAMU STEEL)'],
    ];

    protected $headerRowFound = false;
    protected $headerRow = [];
    protected $logData = [];

    protected $nameColumn = [
        "S/N",
        "S/N",
        "OFFTAKERS NAME",
        "DESIGN CAPACITY\n(MMscfd)",
        "NOMINATIONS\n(MMscfd)",
        "ALLOCATION\n(MMscfd)\nWeekdays",
        "OFFTAKE\n(MMscfd)",
        "INLET",
        "OUTLET",
        "REMARKS",
    ];

    public function model(array $row)
    {
        try {
            if (!$this->headerRowFound) {
                if ($this->isHeaderRow($row)) {
                    $this->headerRowFound = true;
                    return null;
                }
                return null;
            }

            $offTakerName = trim($row[2]); // off-taker name column
            if ($this->isValidOffTaker($offTakerName)) {
                $rowData = [];
                foreach ($this->nameColumn as $index => $header) {
                    // If the index exists in the row and the value is not null
                    if (isset($row[$index]) && $row[$index] !== null) {
                        $rowData[trim($header)] = $row[$index];
                    }
                }

                $this->logData[] = $rowData; // Append
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error processing row: ' . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    public function getLogData()
    {
        return $this->logData;
    }

    private function isHeaderRow(array $row)
    {
        $expectedPattern = [
            "S/N",
            "S/N",
            "OFFTAKERS NAME",
            "DESIGN CAPACITY\n(MMscfd)",
            "NOMINATIONS\n(MMscfd)",
            "ALLOCATION\n(MMscfd)\nWeekdays",
            "OFFTAKE\n(MMscfd)",
            "PRESSURE (BAR)",
            null,
            "REMARKS",
            null,
            null
        ];

        $trimmedRow = array_map('trim', $row);
        $trimmedExpectedPattern = array_map('trim', $expectedPattern);

        return $trimmedRow === $trimmedExpectedPattern;
    }

    private function isValidOffTaker($name)
    {
        foreach ($this->offtakersNames as $offTaker) {
            if (strcasecmp(trim($offTaker['name']), trim($name)) === 0) {
                return true;
            }
        }
        return false;
    }
}
