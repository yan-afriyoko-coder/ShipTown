<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Writer;
use Spatie\QueryBuilder\QueryBuilder;
use SplTempFileObject;

/**
 * Class CsvBuilder.
 */
class CsvBuilder
{
    /**
     * @param QueryBuilder $query
     * @param array $fields
     *
     * @return Writer
     * @throws Exception
     */
    public static function fromQueryBuilder(QueryBuilder $query): Writer
    {
        try {
            $csv = Writer::createFromFileObject(new SplTempFileObject());

            $records = $query->get()->toArray();

            if (count($records) === 0) {
                return $csv;
            }

            $csv->insertOne(array_keys($records[0]));

            $csv->insertAll($records);

            return $csv;
        } catch (CannotInsertRecord $exception) {
            return Writer::createFromString('Error occurred while converting to CSV, see logs for details');
        }
    }

    /**
     * @param array $fromRecords
     * @param array $fields
     *
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
