<?php


namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use Spatie\QueryBuilder\QueryBuilder;
use SplTempFileObject;

/**
 * Class CsvBuilder
 * @package App\Helpers
 */
class CsvBuilder
{
    /**
     * @param QueryBuilder $query
     * @param array $fields
     * @return Writer
     */
    public static function fromQueryBuilder(QueryBuilder $query, array $fields): Writer
    {
        if (empty($fields) or ($fields[0] === "")) {
            return Writer::createFromString('"fields" param not specified, comma delimited, dot notation');
        }

        try {
            $csv = Writer::createFromFileObject(new SplTempFileObject);

            $rows = self::collectOnly($fields, $query->get()->toArray());

            if ($rows->isEmpty()) {
                return $csv;
            }

            $csv->insertOne(array_keys($rows[0]));

            $csv->insertAll($rows);

            return $csv;
        } catch (CannotInsertRecord $exception) {
            return Writer::createFromString('Error occurred while converting to CSV, see logs for details');
        }
    }

    /**
     * @param array $fromRecords
     * @param array $fields
     * @return Collection
     */
    public static function collectOnly(array $fields, array $fromRecords): Collection
    {
        return collect($fromRecords)->map(function ($row) use ($fields) {
            $result = [];

            foreach ($fields as $field) {
                $result[$field] = Arr::get($row, $field);
            }

            return $result;
        });
    }
}
