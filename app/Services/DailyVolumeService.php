<?php

namespace App\Services;

use App\Models\DailyVolume;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DailyVolumeService
{
    /**
     * Validate the provided Daily Volume data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateDailyVolume(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'customer_id' => 'required|integer',
            'customer_site_id' => 'required|integer',
            'volume' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
        ] : [
            'customer_id' => 'sometimes|required|integer',
            'customer_site_id' => 'sometimes|required|integer',
            'volume' => 'sometimes|required|numeric|min:0',
            'rate' => 'sometimes|required|numeric|min:0',
            'amount' => 'sometimes|required|numeric|min:0',
        ];

        // Run the validator with the specified rules
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get all daily volumes with optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithFilters(array $filters = [])
    {
        $query = DailyVolume::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            // Handle date range filters for created_at and updated_at
            if ($key === 'created_at_from') {
                $query->whereDate('created_at', '>=', $value);
            } elseif ($key === 'created_at_to') {
                $query->whereDate('created_at', '<=', $value);
            } elseif ($key === 'updated_at_from') {
                $query->whereDate('updated_at', '>=', $value);
            } elseif ($key === 'updated_at_to') {
                $query->whereDate('updated_at', '<=', $value);
            } else {
                // Apply other filters directly (e.g., status, customer_id)
                $query->where($key, $value);
            }
        }

        // Get the filtered results
        return $query->get();
    }

    /**
     * Get all customer daily volumes.
     *
     * @return \Illuminate\Database\Eloquent\Collection|DailyVolume[]
     */
    public function getAll()
    {
        return DailyVolume::all();
    }

    /**
     * Find a daily volume entry by its ID.
     *
     * @param int $id The ID of the daily volume entry.
     * @return DailyVolume The found daily volume entry.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no entry is found.
     */
    public function getById(int $id): DailyVolume
    {
        return DailyVolume::findOrFail($id);
    }

    /**
     * Create a new daily volume entry.
     *
     * @param array $data The data for creating the daily volume.
     * @return DailyVolume The newly created daily volume entry.
     * @throws \Throwable
     */
    public function create(array $data): DailyVolume
    {
        Log::info('Starting daily volume creation process', ['data' => $data]);

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

            // Validate and create the Daily Volume entry
            $validatedData = $this->validateDailyVolume($data);
            $dailyVolumeCreated = DailyVolume::create($validatedData);

            return $dailyVolumeCreated;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during daily volume creation: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing Daily Volume record.
     *
     * @param array $data The data to update the record with.
     * @return DailyVolume The updated Daily Volume record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): DailyVolume
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting daily volume update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing Daily Volume record by its ID
            $dailyVolume = $this->getById($id);

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
            $validatedData = $this->validateDailyVolume($data, true);

            // Perform the update on the existing Daily Volume record
            $dailyVolume->update($validatedData);

            Log::info('Daily volume updated successfully', ['id' => $dailyVolume->id]);

            return $dailyVolume;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during daily volume update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
