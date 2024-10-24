<?php

namespace App\Services;

use App\Models\GasCost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GasCostService
{
    /**
     * Validate the provided Gas Cost data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateGasCost(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'date_of_entry' => 'required|date',
            'dollar_cost_per_scf' => 'required|numeric|min:0',
            'dollar_rate' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ] : [
            'date_of_entry' => 'sometimes|required|date',
            'dollar_cost_per_scf' => 'sometimes|required|numeric|min:0',
            'dollar_rate' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|boolean',
        ];

        // Run the validator with the specified rules
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get all gas costs with optional filters and pagination.
     *
     * This method allows filtering gas cost records based on the provided filters.
     * It supports date range filters for `date_of_entry`, as well as other column-based filters.
     * The result is paginated.
     *
     * @param array $filters An associative array of filters to apply. Supported keys:
     *                       - 'date_of_entry_from': Filter records where `date_of_entry` is after or on this date.
     *                       - 'date_of_entry_to': Filter records where `date_of_entry` is before or on this date.
     *                       - Additional keys for filtering other columns.
     * @param int $per_page The number of records to return per page. Defaults to 50.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated list of gas costs.
     */
    public function getAllWithFilters(array $filters = [], int $per_page = 50)
    {
        $query = GasCost::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            if ($key !== 'date_of_entry' && $key !== 'dollar_cost_per_scf' && $key !== 'dollar_rate' && $key !== 'status' && $key !== 'date_of_entry_from' && $key !== 'date_of_entry_to') {
                continue;
            }
            switch ($key) {
                case 'date_of_entry_from':
                    $query->whereDate('date_of_entry', '>=', $value);
                    break;
                case 'date_of_entry_to':
                    $query->whereDate('date_of_entry', '<=', $value);
                    break;
                default:
                    // Apply other filters directly (e.g., status)
                    $query->where($key, $value);
                    break;
            }
        }

        // Paginate the results
        return $query->paginate($per_page);
    }

    /**
     * Get paginated gas costs.
     *
     * @param int $per_page Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $per_page = 50)
    {
        return GasCost::paginate($per_page);
    }

    /**
     * Find a gas cost entry by its ID.
     *
     * @param int $id The ID of the gas cost entry.
     * @return GasCost The found gas cost entry.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no entry is found.
     */
    public function getById(int $id): GasCost
    {
        return GasCost::findOrFail($id);
    }

    /**
     * Create a new gas cost entry.
     *
     * @param array $data The data for creating the gas cost.
     * @return GasCost The newly created gas cost entry.
     * @throws \Throwable
     */
    public function create(array $data): GasCost
    {
        Log::info('Starting gas cost creation process', ['data' => $data]);

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
                // Validate and create the Gas Cost entry
                $validatedData = $this->validateGasCost($data);
                $gasCostCreated = GasCost::create($validatedData);

                $gasCost = config("nnpcreusable.GAS_COST_CREATED");
                if (is_array($gasCost) && !empty($gasCost)) {
                    foreach ($gasCost as $queue) {
                        $queue = trim($queue);
                        if (!empty($queue)) {
                            Log::info("Dispatching daily volume creation event to queue: " . $queue);
                            GasCostCreated::dispatch($gasCostCreated->toArray())->onQueue($queue);
                        }
                    }
                }

                return $gasCostCreated;
            } catch (\Throwable $e) {
                Log::error('Unexpected error during gas cost creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing gas cost record.
     *
     * @param array $data The data to update the record with.
     * @return GasCost The updated gas cost record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): GasCost
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting gas cost update process', ['id' => $id, 'data' => $data]);

        try {
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

            // Find the existing Gas Cost record by its ID
            $gasCost = $this->getById($id);

            // Validate the data before performing the update
            $validatedData = $this->validateGasCost($data, true);

            // Perform the update on the existing Gas Cost record
            $gasCost->update($validatedData);

            Log::info('Gas cost updated successfully', ['id' => $gasCost->id]);

            return $gasCost;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during gas cost update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
