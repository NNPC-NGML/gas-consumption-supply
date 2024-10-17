<?php

namespace App\Services;

use App\Http\Resources\GasSituationReportResource;
use App\Jobs\GasConsumption\GasConsumptionCreated;
use App\Jobs\GasConsumption\GasConsumptionUpdated;
use App\Models\GasSituationReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GasSituationReportService
{
    /**
     * Validate the provided Gas Situation Report data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateGasSituationReport(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'customer_id' => 'required|integer',
            'customer_site_id' => 'required|integer',
            'inlet_pressure' => 'required|numeric|min:0',
            'outlet_pressure' => 'required|numeric|min:0',
            'allocation' => 'required|numeric|min:0',
            'nomination' => 'required|numeric|min:0',
        ] : [
            'customer_id' => 'sometimes|required|integer',
            'customer_site_id' => 'sometimes|required|integer',
            'volume' => 'sometimes|required|numeric|min:0',
            'inlet_pressure' => 'sometimes|required|numeric|min:0',
            'outlet_pressure' => 'sometimes|required|numeric|min:0',
            'allocation' => 'sometimes|required|numeric|min:0',
            'nomination' => 'sometimes|required|numeric|min:0',
        ];

        // Run the validator with the specified rules
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get all Gas Situation Reports with optional filters and pagination.
     *
     * This method allows filtering Gas Situation Report records based on the provided filters.
     * It supports date range filters for `created_at` and `updated_at`, as well as
     * other column-based filters. The result is paginated.
     *
     * @param array $filters An associative array of filters to apply. Supported keys:
     *                       - 'created_at_from': Filter records where `created_at` is after or on this date.
     *                       - 'created_at_to': Filter records where `created_at` is before or on this date.
     *                       - 'updated_at_from': Filter records where `updated_at` is after or on this date.
     *                       - 'updated_at_to': Filter records where `updated_at` is before or on this date.
     *                       - Additional keys for filtering other columns.
     * @param int $per_page The number of records to return per page. Defaults to 50.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated list of Gas Situation Reports.
     */
    public function getAllWithFilters(array $filters = [], int $per_page = 50)
    {
        $query = GasSituationReport::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            if($key !== 'customer_id' && $key !== 'customer_site_id' && $key !== 'created_at_from' && $key !== 'created_at_to' && $key !== 'updated_at_from' && $key !== 'updated_at_to' && $key !== 'volume') {
                continue;
            }
            switch ($key) {
                case 'created_at_from':
                    $query->whereDate('created_at', '>=', $value);
                    break;
                case 'created_at_to':
                    $query->whereDate('created_at', '<=', $value);
                    break;
                case 'updated_at_from':
                    $query->whereDate('updated_at', '>=', $value);
                    break;
                case 'updated_at_to':
                    $query->whereDate('updated_at', '<=', $value);
                    break;
                default:
                    // Apply other filters directly (e.g., status, customer_id)
                    $query->where($key, $value);
                    break;
            }
        }

        // Paginate the results
        return $query->paginate($per_page);
    }


    /**
     * Get paginated customer Gas Situation Reports.
     *
     * @param int $per_page Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $per_page = 50)
    {
        return GasSituationReport::paginate($per_page);
    }

    /**
     * Find a Gas Situation Report entry by its ID.
     *
     * @param int $id The ID of the Gas Situation Report entry.
     * @return GasSituationReport The found Gas Situation Report entry.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no entry is found.
     */
    public function getById(int $id): GasSituationReport
    {
        return GasSituationReport::findOrFail($id);
    }

    /**
     * Create a new Gas Situation Report entry.
     *
     * @param array $data The data for creating the Gas Situation Report.
     * @return GasSituationReportResource The resource of the newly created Gas Situation Report entry.
     * @throws \Throwable
     */
    public function create(array $data): GasSituationReportResource
    {
        Log::info('Starting Gas Situation Report creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                // Check if form_field_answers is provided in JSON format
                if (isset($data['form_field_answers'])) {
                    // Decode the JSON data
                    $arrayData = json_decode($data['form_field_answers'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \InvalidArgumentException('Invalid JSON in form_field_answers');
                    }

                    // Optionally, prepare structured data if needed
                    $structuredData = [];
                    foreach ($arrayData as $item) {
                        $structuredData[$item['key']] = $item['value'];
                    }

                    // Merge structured data with the main input data
                    $data = array_merge($data, $structuredData);
                }

                // Validate and create the Gas Situation Report entry
                $validatedData = $this->validateGasSituationReport($data);
                $GasSituationReport = GasSituationReport::create($validatedData);

                // Load relationships
                $GasSituationReport->refresh();
                $GasSituationReport->load(['customer', 'customer_site']);

                // Create a new Gas Situation Report resource
                $resource = new GasSituationReportResource($GasSituationReport);

                $GasSituationReportQueues = config("nnpcreusable.GAS_SITUATION_REPORT_CREATED");
                if (is_array($GasSituationReportQueues) && !empty($GasSituationReportQueues)) {
                    foreach ($GasSituationReportQueues as $queue) {
                        $queue = trim($queue);
                        if (!empty($queue)) {
                            Log::info("Dispatching Gas Situation Report creation event to queue: " . $queue);
                            GasConsumptionCreated::dispatch($resource)->onQueue($queue);
                        }
                    }
                }

                return $resource;
            } catch (\Throwable $e) {
                Log::error('Unexpected error during Gas Situation Report creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }


    /**
     * Update an existing Gas Situation Report record.
     *
     * @param array $data The data to update the record with.
     * @return GasSituationReportResource The updated Gas Situation Report record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): GasSituationReportResource
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting Gas Situation Report update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing Gas Situation Report record by its ID
            $GasSituationReport = $this->getById($id);

            // Check if form_field_answers is provided in JSON format and decode if necessary
            if (isset($data['form_field_answers'])) {
                // Decode the JSON data
                $arrayData = json_decode($data['form_field_answers'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException('Invalid JSON in form_field_answers');
                }

                // Optionally, prepare structured data from the decoded array
                $structuredData = [];
                foreach ($arrayData as $item) {
                    $structuredData[$item['key']] = $item['value'];
                }

                // Merge structured data with the main input data
                $data = array_merge($data, $structuredData);
            }

            // Validate the data before performing the update
            $validatedData = $this->validateGasSituationReport($data, true);

            // Perform the update on the existing Gas Situation Report record
            $GasSituationReport->update($validatedData);

            // Load relationships
            $GasSituationReport->load(['customer', 'customer_site']);

            // Create a new Gas Situation Report resource
            $resource = new GasSituationReportResource($GasSituationReport);

            $GasSituationReportQueues = config("nnpcreusable.GAS_SITUATION_REPORT_UPDATED");
            if (is_array($GasSituationReportQueues) && !empty($GasSituationReportQueues)) {
                foreach ($GasSituationReportQueues as $queue) {
                    $queue = trim($queue);
                    if (!empty($queue)) {
                        Log::info("Dispatching Gas Situation Report update event to queue: " . $queue);
                        GasConsumptionUpdated::dispatch($resource)->onQueue($queue);
                    }
                }
            }

            Log::info('Gas Situation Report updated successfully', ['id' => $GasSituationReport->id]);

            return $resource;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during Gas Situation Report update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
