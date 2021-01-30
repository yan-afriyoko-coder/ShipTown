<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class AbstractDateSelectorWidget
 * @package App\Widgets
 */
abstract class AbstractDateSelectorWidget extends AbstractWidget
{
    /**
     * AbstractDateSelectorWidget constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $stringFilterValue = $this->config['between_dates'] ?? 'today,today end of day';

        $betweenDates = $this->getDates($stringFilterValue);

        $this->config['starting_date'] = $betweenDates->first();
        $this->config['ending_date'] = $betweenDates->last();
    }

    /**
     * @return Collection
     */
    private function getDefaultValues(): Collection
    {
        return collect([
            Carbon::today()->startOfDay(),
            Carbon::today()->endOfDay(),
        ]);
    }

    /**
     * @param string $stringFilterValue
     * @return Collection
     */
    private function getDates(string $stringFilterValue): Collection
    {
        try {
            $datesArray = explode(',', $stringFilterValue);

            $datesCollection = collect($datesArray)->map(function ($stringValue) {
                return Carbon::parse($stringValue);
            });

            if ($datesCollection->count() === 1) {
                $datesCollection->push($datesCollection->first()->clone()->endOfDay());
            }

            if ($datesCollection->isEmpty()) {
                $datesCollection = $this->getDefaultValues();
            }
        } catch (Exception $exception) {
            $datesCollection = $this->getDefaultValues();
        }
        return $datesCollection;
    }

}
