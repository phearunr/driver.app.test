<?php

namespace App\Http\Resources\Drivers;

use Illuminate\Http\Resources\Json\JsonResource;

class DropoffClaimResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'recipe_numbers' => $this->dropoffRecipes->pluck('order_sn')->unique()->implode(','),
            'ordered_quantity' => collect($this->dropoffRecipes->pluck('order_quantity'))->sum(),
            'droffed_quantity' => collect($this->dropoffRecipes->pluck('drop_off_quantity'))->sum(),
            'comments' => $this->comments,
            'scanouter_by' => $this->scanouterBy,
            'scanouted_at' => date('Y-m-d H:i', strtotime($this->scanouted_at)),
            'dropoffer_by' => $this->dropofferBy,
            'dropoffed_at' => $this->dropoffed_at ? date('Y-m-d H:i', strtotime($this->dropoffed_at)) : NULL,
            'status' => $this->status,
            'date' => date('Y-m-d H:i', strtotime($this->created_at))
        ];
    }
}
