<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'equipment_type' => new EquipmentTypeResource($this->equipment_type),
            'serial_number' => $this->serial_number,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
