<?php

namespace App\Http\Controllers;

use App\Helpers\CsvBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Spatie\QueryBuilder\QueryBuilder;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var int
     */
    private int $status_code = 200;

    /**
     * @param QueryBuilder $query
     */
    public function throwCsvDownloadResponse(QueryBuilder $query)
    {
        $fieldsArray = explode(',', request('fields'));
        $content = CsvBuilder::fromQueryBuilder($query, $fieldsArray);

        $filename = request('filename', 'filename_url_param_not_specified.csv');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        response($content, 200, $headers)->throwResponse();
    }

    /**
     * @param QueryBuilder $query
     * @param int $defaultPerPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedResult(QueryBuilder $query, $defaultPerPage = 10): LengthAwarePaginator
    {
        $perPage = request()->get('per_page', $defaultPerPage);

        $requestQuery = request()->query();

        return $query->paginate($perPage)
            ->appends($requestQuery);
    }

    /**
     * @param $status_code
     * @param $message
     * @return JsonResponse
     */
    public function getResponse($status_code, $message): JsonResponse
    {
        return response()->json(
            [
                "message" => $message
            ],
            $status_code
        );
    }

    public function throwJsonResponse($message)
    {
        response()->json(
            ["message" => $message],
            $this->getStatusCode()
        )->throwResponse();
    }

    public function respondNotAllowed405($message = 'Method not allowed')
    {
        $this->setStatusCode(405)->throwJsonResponse($message);
    }

    public function respondOK200($message = null)
    {
        $this->setStatusCode(200)->throwJsonResponse($message);
    }

    public function respondNotFound($message = "Not Found!")
    {
        $this->setStatusCode(404)->throwJsonResponse($message);
    }

    public function respondBadRequest($message = "Bad request")
    {
        $this->setStatusCode(400)->throwJsonResponse($message);
    }

    public function respond403Forbidden($message = "Forbidden")
    {
        $this->setStatusCode(403)->throwJsonResponse($message);
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

    /**
     * @param string $pdfString
     * @return ResponseFactory|Response
     */
    public function getPdfResponse(string $pdfString)
    {
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ];

        return response($pdfString, 200, $headers);
    }
}
