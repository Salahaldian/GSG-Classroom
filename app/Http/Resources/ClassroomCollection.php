<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClassroomCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->collection->map(function ($model) {
            $data[] = [
                'id' => $model->id,
                'name' => $model->name,
                'code' => $model->code,
                'cover_image' => $model->cover_image_url,
                'meta' => [
                    'section' => $model->section,
                    'room' => $model->room,
                    'subject' => $model->subject,
                    'students_count' => $model->students_count ?? 0,
                    'theme' => $model->theme
                ],
                'user' => [
                    'name' => $model->user?->name,
                ]
            ];
        });
        return [
            'data' => $data,
            'links' => [
                'self' => 'link-value',
            ],
        ];
        /*
        $this->withoutWrapping();
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'cover_image' => $this->cover_image_url,
            'meta' => [
                'section' => $this->section,
                'room' => $this->room,
                'subject' => $this->subject,
                'students_count' => $this->students_count ?? 0,
                'theme' => $this->theme
            ],
            'user' => [
                'name' => $this->user->name,
            ]
        ];
        */
    }
}
