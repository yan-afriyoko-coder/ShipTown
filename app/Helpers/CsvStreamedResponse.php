<?php

namespace App\Helpers;

use League\Csv\Writer;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvStreamedResponse
{
    public static function fromQueryBuilder(QueryBuilder $query, string $filename): StreamedResponse|Writer
    {
        return new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            $hasExportedHeaders = false;

            $query->chunk(10000, function ($records) use ($handle, $hasExportedHeaders) {
                if (! $hasExportedHeaders) {
                    fputcsv($handle, array_keys($records->first()->toArray()));
                    $hasExportedHeaders = true;
                }

                foreach ($records as $record) {
                    fputcsv($handle, $record->toArray());
                }
            });

            fclose($handle);
        }, 200, [
            'Cache-Control' => 'no-store, no-cache',
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
