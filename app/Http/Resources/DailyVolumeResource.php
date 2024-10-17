<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="DailyVolume",
 *     type="object",
 *     title="Daily Volume",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the existing Daily Volume"),
 *     @OA\Property(property="customer_id", type="integer", example=1, description="ID of the customer"),
 *     @OA\Property(property="customer_site_id", type="integer", example=1, description="ID of the customer site"),
 *     @OA\Property(property="abnormal_status", type="string", example="normal", description="to indicate if the volume is normal or abnormal"),
 *     @OA\Property(property="volume", type="float", example=1, description="Volume in Scf"),
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
        // Fetch historical average for this customer (adjust as needed)
        $sevenDaysBefore = Carbon::parse($this->created_at)->subDays(7);

        // Fetch the historical average for the customer within the 7 days leading up to this record's creation date
        $averageVolume = DB::table('daily_volumes')
            ->where('customer_site_id', $this->customer_site_id)
            ->whereBetween('created_at', [$sevenDaysBefore, $this->created_at])
            ->avg('volume');

        // Set thresholds (e.g., 50% below and above the average volume)
        $lowThreshold = 0.8 * $averageVolume;
        $highThreshold = 1.2 * $averageVolume;

        // Determine the status based on thresholds
        $status = 'normal';
        if ($this->volume < $lowThreshold) {
            $status = 'low';
        } elseif ($this->volume > $highThreshold) {
            $status = 'high';
        }
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'customer_site_id' => $this->customer_site_id,
            'volume' => $this->volume,
            'remark' => $this->remark,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'abnormal_status' => $status,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'customer_site' => new CustomerSiteResource($this->whenLoaded('customer_site')),
        ];
    }
}
