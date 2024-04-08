<?php

namespace App\Http\Controllers;

use App\Helpers\CsvBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use League\Csv\Exception;
use Spatie\QueryBuilder\QueryBuilder;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * @var int
     */
    private int $status_code = 200;

    /**
     * @param QueryBuilder $query
     * @throws Exception
     */
    public function throwCsvDownloadResponse(QueryBuilder $query)
    {
        $content = CsvBuilder::fromQueryBuilder($query);

        $filename = request('filename', 'filename_url_param_not_specified.csv');
        $headers = [
            'Content-Type'              => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => 'attachment; filename="'.$filename.'"',
        ];

        response($content, 200, $headers)->throwResponse();
    }

    /**
     * @param string $pdfString
     */
    public function throwPdfResponse(string $pdfString)
    {
        $headers = [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline',
        ];

        response($pdfString, $this->status_code, $headers)->throwResponse();
    }

    public function throwJsonResponse($message)
    {
        response()->json(
            ['message' => $message],
            $this->getStatusCode()
        )->throwResponse();
    }

    /**
     * @param QueryBuilder $query
     * @param int $defaultPerPage
     *
     * @return LengthAwarePaginator
     */
    public function getPaginatedResult(QueryBuilder $query, int $defaultPerPage = 10): LengthAwarePaginator
    {
        $perPage = request()->get('per_page', $defaultPerPage);

        $requestQuery = request()->query();

        return $query->paginate($perPage)->appends($requestQuery);
    }

    public function respondNotAllowed405($message = 'Method not allowed')
    {
        $this->setStatusCode(405)->throwJsonResponse($message);
    }

    public function respondOK200($message = null)
    {
        $this->setStatusCode(200)->throwJsonResponse($message);
    }

    public function respondNotFound($message = 'Not Found!')
    {
        $this->setStatusCode(404)->throwJsonResponse($message);
    }

    public function respondBadRequest($message = 'Bad request')
    {
        $this->setStatusCode(400)->throwJsonResponse($message);
    }

    public function respond403Forbidden($message = 'Forbidden')
    {
        $this->setStatusCode(403)->throwJsonResponse($message);
    }


    public function respond503ServiceUnavailable($message = 'Service Unavailable')
    {
        $this->setStatusCode(503)->throwJsonResponse($message);
    }

    public function setStatusCode($code): Controller
    {
        $this->status_code = $code;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->status_code;
    }
}
