<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RunScheduledJobsRequest extends FormRequest
{
    public function authorize():bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'job'       => 'sometimes|string',
            'schedule'  => 'sometimes|string|in:EveryMinute,EveryFiveMinutes,EveryTenMinutes,EveryHour,EveryDay,EveryWeek,EveryMonth,SyncRequest,null',
        ];
    }
}
