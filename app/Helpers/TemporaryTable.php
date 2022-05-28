<?php

namespace App\Helpers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TemporaryTable
{
    public static function create($table_name, $subQuery): Builder
    {
        $str = /** @lang text */ 'CREATE TEMPORARY TABLE %s AS (%s)';
        $finalQuery = sprintf($str, $table_name, $subQuery->toSql());

        DB::statement($finalQuery, $subQuery->getBindings());

        return DB::table($table_name);
    }
}
