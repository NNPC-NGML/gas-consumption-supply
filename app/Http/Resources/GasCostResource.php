<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="GasCost",
 *     type="object",
 *     title="Gas Cost",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the gas cost record"),
 *     @OA\Property(property="date_of_entry", type="string", format="date", example="2024-09-23", description="Date of entry"),
 *     @OA\Property(property="dollar_cost_per_scf", type="float", example=1.0, description="Dollar cost per SCF"),
 *     @OA\Property(property="dollar_rate", type="float", example=1.0, description="Dollar rate in NGN per dollar cost per SCF"),
 *     @OA\Property(property="status", type="boolean", example=true, description="Status of the gas cost record"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 * )
 */
class GasCostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date_of_entry' => $this->date_of_entry->toDateString(),
            'dollar_cost_per_scf' => $this->dollar_cost_per_scf,
            'dollar_rate' => $this->dollar_rate,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
