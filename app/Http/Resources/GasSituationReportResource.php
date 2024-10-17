<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GasSituationReportResource extends JsonResource
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
            'inlet_pressure' => $this->inlet_pressure,
            'outlet_pressure' => $this->outlet_pressure,
            'allocation' => $this->allocation,
            'nomination' => $this->nomination,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'customer_site' => new CustomerSiteResource($this->whenLoaded('customer_site')),
        ];
    }
}
