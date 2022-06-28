<?php

namespace App\Modules\Reports\src\Models;

use App\Helpers\CsvBuilder;
use App\Modules\Reports\src\Http\Resources\ReportResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Spatie\QueryBuilder\QueryBuilder;

class Report extends Model
{
    protected $table = 'report';
    protected string $report_name = 'Report';

    public array $toSelect = [];

    public array $fields = [];
    /**
     * @var mixed
     */
    public $baseQuery;

    private array $allowedFilters = [];
    private array $fieldAliases = [];

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function response($request)
    {
        if ($request->has('filename')) {
            return $this->csvDownload();
        }

        return $this->view();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function view()
    {
        try {
            $queryBuilder = $this->queryBuilder()
                ->limit(request('per_page', 10));
        } catch (InvalidFilterQuery $exception) {
            printf($exception->getMessage());
            die(400);
        }


        $resource = ReportResource::collection($queryBuilder->get());

        $data = [
            'report_name' => $this->report_name ?? $this->table,
            'fields' => $resource->count() > 0 ? array_keys((array)json_decode($resource[0]->toJson())) : [],
            'data' => $resource
        ];

        return view('reports.inventory', $data);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function queryBuilder(): QueryBuilder
    {
        $this->fieldAliases = [];

        foreach ($this->fields as $alias => $field) {
            $this->fieldAliases[] = $alias;
        }

        return QueryBuilder::for($this->baseQuery)
            ->select($this->getSelectFields())
            ->allowedFilters($this->getAllowedFilters())
            ->allowedSorts($this->fieldAliases);
    }

    /**
     * @param AllowedFilter $filter
     * @return $this
     */
    public function addFilter(AllowedFilter $filter): Report
    {
        $this->allowedFilters[] = $filter;

        return $this;
    }

    /**
     * @return array
     */
    private function getAllowedFilters(): array
    {
        $filters = collect($this->allowedFilters);

        $filters = $filters->merge($this->addExactFilters());
        $filters = $filters->merge($this->addContainsFilters());
        $filters = $filters->merge($this->addBetweenFilters());
        $filters = $filters->merge($this->addGreaterThan());
        $filters = $filters->merge($this->addLowerThan());

        return $filters->toArray();
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getSelectFields(): array
    {
        $fieldsToSelect = collect(explode(',', request()->get('select', '')))->filter();

        if ($fieldsToSelect->isEmpty()) {
            $fieldsToSelect = collect(array_keys($this->fields));
        }

        return $fieldsToSelect
            ->map(function ($alias) {
                if ($this->fields[$alias] instanceof Expression) {
                    return $this->fields[$alias];
                }

                return $this->fields[$alias] . ' as ' . $alias;
            })
            ->toArray();
    }

    /**
     * @return array
     */
    private function addExactFilters(): array
    {

        $allowedFilters = [];

        // add exact filters
        collect($this->fields)
            ->each(function ($full_field_name, $alias) use (&$allowedFilters) {
                $allowedFilters[] = AllowedFilter::exact($alias, $full_field_name);
            });

        return $allowedFilters;
    }


    /**
     * @return array
     */
    private function addContainsFilters(): array
    {
        $allowedFilters = [];

        collect($this->fields)
            ->filter(function ($value, $key) {
                $type = data_get($this->casts, $key);

                return in_array($type, ['string', null]);
            })
            ->each(function ($record, $alias) use (&$allowedFilters) {
                $filterName = $alias . '_contains';

                $allowedFilters[] = AllowedFilter::partial($filterName, $record);
            });

        return $allowedFilters;
    }

    /**
     * @return array
     */
    private function addBetweenFilters(): array
    {
        $allowedFilters = [];

        collect($this->casts)
            ->filter(function ($type) {
                return $type === 'float';
            })
            ->each(function ($record, $alias) use (&$allowedFilters) {
                $filterName = $alias . '_between';

                $allowedFilters[] = AllowedFilter::callback($filterName, function ($query, $value) use ($alias) {
                    // we add this to make sure query returns no records if array of two values is not specified
                    if ((!is_array($value)) or (count($value) != 2)) {
                        $query->whereRaw('1=2');
                        return;
                    }

                    $query->whereBetween($this->fields[$alias], [floatval($value[0]), floatval($value[1])]);
                });
            });

        return $allowedFilters;
    }

    /**
     * @return array
     */
    private function addGreaterThan(): array
    {
        $allowedFilters = [];

        collect($this->casts)
            ->filter(function ($type) {
                return $type === 'float';
            })
            ->each(function ($record, $alias) use (&$allowedFilters) {
                $filterName = $alias . '_greater_than';

                $allowedFilters[] = AllowedFilter::callback($filterName, function ($query, $value) use ($alias) {
                    // we add this to make sure query returns no records if array of two values is not specified
                    if ((!is_array($value)) or (count($value) != 2)) {
                        $query->whereRaw('1=2');
                        return;
                    }

                    $query->where($this->fields[$alias], '>', floatval($value));
                });
            });

        return $allowedFilters;
    }

    /**
     * @return array
     */
    private function addLowerThan(): array
    {
        $allowedFilters = [];

        collect($this->casts)
            ->filter(function ($type) {
                return $type === 'float';
            })
            ->each(function ($record, $alias) use (&$allowedFilters) {
                $filterName = $alias . '_lower_than';

                $allowedFilters[] = AllowedFilter::callback($filterName, function ($query, $value) use ($alias) {
                    // we add this to make sure query returns no records if array of two values is not specified
                    if ((!is_array($value)) or (count($value) != 2)) {
                        $query->whereRaw('1=2');
                        return;
                    }

                    $query->where($this->fields[$alias], '<', floatval($value));
                });
            });

        return $allowedFilters;
    }
}
