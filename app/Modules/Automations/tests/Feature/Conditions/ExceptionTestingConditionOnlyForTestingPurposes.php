<?php

namespace App\Modules\Automations\tests\Feature\Conditions;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class ExceptionTestingConditionOnlyForTestingPurposes extends BaseOrderConditionAbstract
{
    /**
     * @throws Exception
     */
    public static function ordersQueryScope(Builder $query, $expected_status_code): Builder
    {
        throw new Exception('This exception should be handled by automation');
    }
}
