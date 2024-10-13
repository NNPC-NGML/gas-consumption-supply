<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CustomerDailyVolume",
 *     type="object",
 *     title="Customer Daily Volume",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the existing Daily Volume"),
 *     @OA\Property(property="customer_id", type="integer", example=1, description="ID of the customer"),
 *     @OA\Property(property="customer_site_id", type="integer", example=1, description="ID of the customer site"),
 *     @OA\Property(property="volume", type="float", example=1, description="Volume in Scf"),
 *     @OA\Property(property="rate", type="float", example=1, description="Rate in NGN/Scf"),
 *     @OA\Property(property="amount", type="float", example=1, description="Amount from (volume * rate) in NGN"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 *     @OA\Property(
 *         property="customer",
 *         ref="#/components/schemas/CustomerResource",
 *         description="Details of the customer"
 *     ),
 *     @OA\Property(
 *         property="customer_site",
 *         ref="#/components/schemas/CustomerSiteResource",
 *         description="Details of the customer site"
 *     ),
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
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'customer_site' => new CustomerSiteResource($this->whenLoaded('customer_site')),
        ];
    }
}
