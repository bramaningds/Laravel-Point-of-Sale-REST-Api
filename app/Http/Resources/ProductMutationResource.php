<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductMutationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'mutation_type' => $this->mutation_type,
            'mutation_timestamp' => $this->mutation_timestamp,
            'debet' => $this->debet,
            'credit' => $this->credit,
            'balance' => $this->balance,
        ];
    }
}
