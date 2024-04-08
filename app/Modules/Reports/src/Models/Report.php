<?php

namespace App\Modules\Reports\src\Models;

use App\Exceptions\InvalidSelectException;
use App\Helpers\CsvBuilder;
use App\Modules\Reports\src\Http\Resources\ReportResource;
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
        $request = $request ?? request();

        if ($request->has('filename')) {
            return $this->toCsvFileDownload();
        }

        return $this->view();
    }

    protected function view(): mixed
    {
        $limit = request('per_page', $this->perPage);
        $offset = request('page', 0) * $limit;

        try {
            $queryBuilder = $this->queryBuilder()->offset($offset)->limit($limit)->get();
        } catch (InvalidFilterQuery | InvalidSelectException $ex) {
            return response($ex->getMessage(), $ex->getStatusCode());
        }

        $resource = ReportResource::collection($queryBuilder);

        $data = [
            'report_name' => $this->report_name ?? $this->table,
            'fields' =>  array_keys($this->fields),
            'data' => $resource,
            'pagination' => [
                'per_page' => $limit,
                'page' => request('page', 0),
            ]
        ];

        $data['field_links'] = collect($data['fields'])->map(function ($field) {

            $sortIsDesc = request()->has('sort') && str_starts_with(request()->sort, '-');
            $currentSortName = str_replace('-', '', request()->sort);
            $isCurrent = $currentSortName === $field;
            $url = request()->fullUrlWithQuery(['sort' => $isCurrent && !$sortIsDesc ? "-".$field : $field]);

            return [
                'name' => $field,
                'url' => $url,
                'is_current' => $isCurrent,
                'is_desc' => $sortIsDesc,
                'display_name' => str_replace('_', ' ', ucwords($field, '_')),
                'type' => $this->getFieldType($field),
                'operators' => $this->getFieldTypeOperators($field),
            ];
        });

        return view($this->view, $data);
    }

    public static function toJsonResource(): JsonResource
    {
        return JsonResource::make((new static())->queryBuilder()
            ->offset(( request('page', 1) - 1) * request('per_page', 100))
            ->limit(request('per_page', 100))
            ->get());
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
