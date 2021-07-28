<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MailTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->getKey(),
            'name'          => $this->name,
            'subject'       => $this->subject,
            'html_template' => $this->html_template,
            'text_template' => $this->text_template,
        ];
    }
}
