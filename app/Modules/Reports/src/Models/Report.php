<?php

namespace App\Modules\Reports\src\Models;

use App\Helpers\CsvBuilder;
use App\Models\Inventory;
use App\Modules\Reports\src\Http\Resources\ReportResource;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class Report extends Model
{
    protected $table = 'report';

    public array $toSelect = [];

    protected array $fields;
    protected $baseQuery;

    private array $fieldAliases = [];
    private array $fieldSelects = [];

    public function response($request)
    {
        if ($request->has('filename')) {
            return $this->csvDownload();
        }

        return $this->view();
    }

    public function csvDownload()
    {
        $csv = CsvBuilder::fromQueryBuilder(
            $this->queryBuilder(),
            $this->fieldAliases
        );

        return response((string) $csv, 200, [
            'Content-Type'              => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => 'attachment; filename="'.request('filename', 'report.csv').'"',
        ]);
    }

    private function view()
    {
        $resource = ReportResource::collection(
            $this->queryBuilder()
                ->paginate(request('per_page', 10))
                ->appends(request()->query())
        );

        $data = [
            'fields' => $resource->count() > 0 ? array_keys((array)json_decode($resource[0]->toJson())) : [],
            'data' => $resource
        ];

        return view('reports.inventory', $data);
    }

    public function queryBuilder(): QueryBuilder
    {
        foreach ($this->fields as $field => $alias) {
            $this->fieldSelects[] = "$field as $alias";
            $this->fieldAliases[] = $alias;
        }

        $allowedFilters = [];

        collect($this->fields)
            ->each(function ($record, $key) use (&$allowedFilters) {
                $allowedFilters[] = AllowedFilter::exact($record, $key);
            });

        // add between filters
        collect($this->casts)->filter(function ($type) {
            return $type === 'float';
        })
        ->each(function ($record, $key) use (&$allowedFilters) {
            $allowedFilters[] = AllowedFilter::callback($key . '_between', function ($query, $value) use ($record, $key) {
                // we add this to make sure query returns no records
                // if array of two values is not specified
                if ((! is_array($value)) or (count($value) != 2)) {
                    $query->whereRaw('1=2');
                    return;
                }

                $query->whereBetween($key, [floatval($value[0]), floatval($value[1])]);
            });
        });

        $queryBuilder = QueryBuilder::for($this->baseQuery)
            ->select($this->fieldSelects)
            ->allowedFilters($allowedFilters)
            ->allowedSorts($this->fieldAliases);

        if (request()->has('select')) {
            $queryBuilder->select(explode(',', request('select')));
        }

        return $queryBuilder;
    }
}
