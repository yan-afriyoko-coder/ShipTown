<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class TemporaryTable
{
    public static function create($table_name, $subQuery): bool
    {
        DB::statement('DROP TEMPORARY TABLE IF EXISTS '.$table_name);

        $finalQuery = sprintf(
            /** @lang text */
            'CREATE TEMPORARY TABLE %s AS (%s)',
            $table_name,
            $subQuery->toSql()
        );

        return DB::statement($finalQuery, $subQuery->getBindings());
    }

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
