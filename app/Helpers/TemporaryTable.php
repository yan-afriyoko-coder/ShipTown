<?php

namespace App\Helpers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TemporaryTable
{
    /**
     * @param $table_name
     * @param $subQuery
     * @return bool
     */
    public static function create($table_name, $subQuery): bool
    {
        $finalQuery = sprintf(
            /** @lang text */
            'CREATE TEMPORARY TABLE %s AS (%s)',
            $table_name,
            $subQuery->toSql()
        );

        return DB::statement($finalQuery, $subQuery->getBindings());
    }

    /**
     * @param string $table_name
     * @param string $columnsStatement
     * @return bool
     */
    public static function createEmpty(
        string $table_name,
        string $columnsStatement = 'id bigint(20) unsigned NOT NULL AUTO_INCREMENT'
    ): bool {
        $finalQuery = sprintf(
            /** @lang text */
            'CREATE TEMPORARY TABLE %s (%s)',
            $table_name,
            $columnsStatement
        );

        return DB::statement($finalQuery);
    }
}
