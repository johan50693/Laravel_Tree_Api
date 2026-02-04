<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = $this->getLanguage($request);
        $timezone = $request->input('timezone', 'UTC');

        return [
            'id' => $this->id,
            'parent' => $this->parent ? [
                'id' => $this->parent->id,
                'title' => $this->generateTitle($this->parent->id, $lang),
                'created_at' => $this->parent->created_at->setTimezone($timezone)->toDateTimeString(),
            ] : null,
            'title' => $this->generateTitle($this->id, $lang),
            'created_at' => $this->created_at->setTimezone($timezone)->toDateTimeString(),
            'children' => NodeResource::collection($this->whenLoaded('children')),
        ];
    }

    private function getLanguage(Request $request): string
    {
        $lang = $request->header('Accept-Language');
        if ($lang) {
            if (strpos($lang, ',') !== false) {
                $lang = explode(',', $lang)[0];
            }
            if (strpos($lang, '-') !== false) {
                $lang = explode('-', $lang)[0];
            }
        }
        return $lang ?: 'en';
    }

    private function generateTitle(int $id, string $lang): string
    {
        try {
            $numberToWords = new \NumberToWords\NumberToWords();
            $transformer = $numberToWords->getNumberTransformer($lang);
            return $transformer->toWords($id);
        } catch (\Exception $e) {
            try {
                $numberToWords = new \NumberToWords\NumberToWords();
                $transformer = $numberToWords->getNumberTransformer('en');
                return $transformer->toWords($id);
            } catch (\Exception $ex) {
                return (string) $id;
            }
        }
    }
}
