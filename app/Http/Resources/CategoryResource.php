<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $data = parent::toArray($request);

        $data['image_url'] = url('/storage/' . $data['image']);

        // Include image_url for parent category
        if ($this->parent) {
            $data['parent']['image_url'] = url('/storage/' . $this->parent['image']);
        }

        // Include image_url for all levels of children
        if ($this->children) {
            $data['children'] = $this->transformChildren($this->children);
        }

        return $data;
    }

    /**
     * Recursively transform children and grandchildren.
     *
     * @param  mixed  $children
     * @return mixed
     */
    protected function transformChildren($children)
    {
        return $children->map(function ($child) {
            $childData = $child->toArray();

            $childData['image_url'] = url('/storage/' . $childData['image']);

            // Recursively transform grandchildren
            if ($child->children) {
                $childData['children'] = $this->transformChildren($child->children);
            }

            return $childData;
        });
    }
}
