<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CustomerDdqExisting",
 *     type="object",
 *     title="Customer DDQ Existing",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the existing Daily Volume"),
 *     @OA\Property(property="customer_id", type="integer", example=1, description="ID of the customer"),
 *     @OA\Property(property="customer_site_id", type="integer", example=1, description="ID of the customer site"),
 *     @OA\Property(property="volume", type="float", example=1, description="Volume in Scf"),
 *     @OA\Property(property="rate", type="float", example=1, description="Rate in NGN/Scf"),
 *     @OA\Property(property="amount", type="float", example=1, description="Amount from (volume * rate) in NGN"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 * )
 */
class DailyVolumeResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'customer_site_id' => $this->customer_site_id,
            'volume' => $this->volume,
            'rate' => $this->rate,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
