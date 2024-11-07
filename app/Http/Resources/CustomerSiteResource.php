<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSiteResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="CustomerSiteResource",
     *     type="object",
     *     title="Customer site details",
     *     @OA\Property(property="id", type="integer", example=1, description="ID of the customer site"),
     *     @OA\Property(property="customer_id", type="integer", example=1, description="ID of the customer"),
     *     @OA\Property(property="site_address", type="string", example=123, description="the address of the site"),
     *     @OA\Property(property="ngml_zone_id", type="integer", example=1, description="the ngml zone of the site"),
     *     @OA\Property(property="site_name", type="string", example=123, description="the name of the site"),
     *     @OA\Property(property="site_contact_person_name", type="string", example=1, description="the name of the site contact person"),
     *     @OA\Property(property="email", type="string", example="ngml@gmail.com", description="the email of the site contact person"),
     *     @OA\Property(property="site_contact_person_signature", type="string", example="/path/to/file.png", description="site contact person signature"),
     *     @OA\Property(property="site_contact_person_email", type="string", example="ngml@gmail.com", description="site contact person email"),
     *     @OA\Property(property="site_existing_status", type="integer", example=1, description="site existing status"),
     *     @OA\Property(property="phone_number", type="string", example=229903999, description="the phone number of the site contact person"),
     *     @OA\Property(property="site_contact_person_phone_number", type="string", example=08000000, description="the phone number of the site contact person"),
     *     @OA\Property(property="status", type="boolean", example=true, description="site status"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation date of the site"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update date of the site")
     * )
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
