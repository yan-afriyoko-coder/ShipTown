<?php

namespace App\Modules\Reports\src\Models;

use App\Helpers\CsvBuilder;
use App\Modules\Reports\src\Http\Resources\ReportResource;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class Report extends Model
{
    protected $table = 'report';
    protected string $report_name = 'Report';

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
            'Cache-Control'             => 'no-store, no-cache',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => 'attachment; filename="'.request('filename', 'report.csv').'"',
        ]);
    }

    private function view()
    {
        $resource = ReportResource::collection(
            $this->queryBuilder()
                ->limit(request('per_page', 10))
                ->get()
        );

        $data = [
            'report_name' => $this->report_name ?? $this->table,
            'fields' => $resource->count() > 0 ? array_keys((array)json_decode($resource[0]->toJson())) : [],
            'data' => $resource
        ];

        return view('reports.inventory', $data);
    }

    public function queryBuilder(): QueryBuilder
    {
        $this->fieldSelects = [];
        $this->fieldAliases = [];

        foreach ($this->fields as $alias => $field) {
            $this->fieldSelects[] = "$field as $alias";
            $this->fieldAliases[] = $alias;
        }

        return QueryBuilder::for($this->baseQuery)
            ->select($this->getSelectFields())
            ->allowedFilters($this->getAllowedFilters())
            ->allowedSorts($this->fieldAliases);
    }

    /**
     * @return array
     */
    private function getAllowedFilters(): array
    {
        $filters = [];

        // add exact filters
        collect($this->fields)
            ->each(function ($full_field_name, $alias) use (&$filters) {
                $filters[] = AllowedFilter::exact($alias, $full_field_name);
            });

        // add between filters
        collect($this->casts)
            ->filter(function ($type) {
                return $type === 'float';
            })
            ->each(function ($record, $alias) use (&$filters) {
                $filters[] = AllowedFilter::callback($alias . '_between', function ($query, $value) use ($alias) {
                    // we add this to make sure query returns no records
                    // if array of two values is not specified
                    if ((! is_array($value)) or (count($value) != 2)) {
                        $query->whereRaw('1=2');
                        return;
                    }

                    $query->whereBetween($this->fields[$alias], [floatval($value[0]), floatval($value[1])]);
                });
            });

        return $filters;
    }

    /**
     * @return array
     */
    private function getSelectFields(): array
    {
        if (request()->has('select')) {
            return collect(explode(',', request('select')))
                ->map(function ($alias) {
                    return $this->fields[$alias] . ' as ' . $alias;
                })
                ->toArray();
        }

        return $this->fieldSelects;
    }
}
