<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="CustomerResource",
     *     type="object",
     *     title="Customer details",
     *     @OA\Property(property="id", type="integer", example=1, description="ID of the customer"),
     *     @OA\Property(property="company_name", type="string", example="Dangote Energy Limited", description="Name of the customer"),
     *     @OA\Property(property="email", type="string", example="ngml@gmail.com", description="Email of the customer"),
     *     @OA\Property(property="phone_number", type="string", example="08008000", description="Phone number of the customer"),
     *     @OA\Property(property="status", type="boolean", example=true, description="Status of the customer"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation date of the customer"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update date of the customer")
     * )
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
