<?php

namespace App\Modules\PrintNode\src\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PrintJob.
 *
 * @property int    $printer_id
 *                                Print job title displayed in system
 * @property string $title
 *                                Content type to be printed
 * @property string $content_type
 *                                Content to be printed
 * @property string $content
 *                                PDF filename or content
 * @property string $pdf
 *                                Url to PDF
 * @property string $pdf_url
 *                                Time in seconds to expire if cannot be delivered to printer
 * @property int    $expire_after
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob query()
 * @mixin \Eloquent
 */
class PrintJob extends Model
{
    public function save(array $options = []): bool
    {
        return false;
    }

    protected $fillable = [
        'printer_id', 'title', 'content_type', 'content', 'expire_after', 'pdf_url', 'pdf',
    ];

    protected $attributes = [
        'title'        => '',
        'content_type' => 'pdf_uri',
        'expireAfter'  => 15,
    ];

    public function setPdfAttribute($value)
    {
        $this->content_type = 'pdf_base64';
        $this->content = $value;

        if (@is_file($value)) { // if file exists and is a file and not a directory
            $this->content = file_get_contents($value);
        }
    }

    public function setPdfUrlAttribute($value)
    {
        $this->content_type = 'pdf_uri';
        $this->content = $value;
    }

    public function toPrintNodePayload(): array
    {
        return [
            'source'      => 'Products Management',
            'expireAfter' => $this->expire_after, // seconds
            'printerId'   => $this->printer_id,
            'title'       => $this->title,
            'contentType' => $this->content_type,
            'content'     => $this->content,
        ];
    }
}
