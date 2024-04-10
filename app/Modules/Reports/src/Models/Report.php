<?php

namespace App\Modules\Reports\src\Models;

use App\Helpers\CsvBuilder;
use File;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use League\Csv\Exception;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;

class Report extends ReportBase
{
    public function response($request = null): mixed
    {
        switch (File::extension(request('filename'))) {
            case 'csv':
                return $this->toCsvFileDownload();
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
    /**
     * @throws Exception
     */
    public function toCsvFileDownload(): Response|Application|ResponseFactory
    {
        $csv = CsvBuilder::fromQueryBuilder($this->queryBuilder());

        return response((string)$csv, 200, [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . request('filename', 'report.csv') . '"',
        ]);
    }
}
