<?php

namespace App\Modules\Reports\src\Models;

use App\Helpers\CsvStreamedResponse;
use File;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;

class Report extends ReportBase
{
    public function response($request = null): mixed
    {
        switch (File::extension(request('filename'))) {
            case 'csv':
                return CsvStreamedResponse::fromQueryBuilder($this->queryBuilder(), request('filename'));
            case 'json':
                return $this->toJsonResource();
            default:
                return $this->view();
        }
    }

    protected function view(): mixed
    {
        try {
            $view = request('view', $this->view);

            return view($view, [
                'data' => $this->getRecords(),
                'meta' => $this->getMetaData(),
            ]);
        } catch (InvalidFilterQuery $ex) {
            return response($ex->getMessage(), $ex->getStatusCode());
        }
    }

    public function toJsonResource(): JsonResource
    {
        return JsonResource::make([
            'data' => $this->getRecords(),
            'meta' => $this->getMetaData(),
        ]);
    }

    public static function json(): JsonResource
    {
        $report = new static();

        return $report->toJsonResource();
    }
}
