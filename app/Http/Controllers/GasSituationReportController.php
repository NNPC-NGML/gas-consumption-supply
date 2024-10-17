<?php

namespace App\Http\Controllers;

use App\Http\Resources\GasSituationReportResource;
use App\Models\GasSituationReport;
use App\Services\GasSituationReportService;
use Illuminate\Http\Request;

class GasSituationReportController extends Controller
{
    /**
     * The GasSituationReportService instance.
     *
     * @var GasSituationReportService
     */
    protected GasSituationReportService $gasSituationReportService;

    /**
     * GasSituationReportController constructor.
     *
     * @param GasSituationReportService $gasSituationReportService
     */
    public function __construct(GasSituationReportService $gasSituationReportService)
    {
        $this->gasSituationReportService = $gasSituationReportService;
    }

    /**
     * @OA\Get(
     *     path="/api/gas-situation-reports",
     *     tags={"Gas Situation Reports"},
     *     summary="Get a list of Gas Situation Reports with filters and pagination",
     *     description="Fetches a list of Gas Situation Reports, with optional filtering based on fields in the Gas Situation Reports table, and supports pagination.",
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page",
     *         @OA\Schema(type="integer", example=50)
     *     ),
     *     @OA\Parameter(
     *         name="created_at_from",
     *         in="query",
     *         description="Filter by start date for created_at",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="created_at_to",
     *         in="query",
     *         description="Filter by end date for created_at",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="updated_at_from",
     *         in="query",
     *         description="Filter by start date for updated_at",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="updated_at_to",
     *         in="query",
     *         description="Filter by end date for updated_at",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="customer_id",
     *         in="query",
     *         description="Filter by customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/gas-situation-reports?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/GasSituationReport")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            // Get all request parameters for filtering
            $filters = $request->all();

            // Extract 'per_page' if provided, default to 50
            $per_page = $request->input('per_page', default: 50);

            // Pass the filters and per_page to the service for query building
            $gasSituationReports = $this->gasSituationReportService->getAllWithFilters($filters, $per_page);

            // Return the paginated results with additional pagination metadata
            return GasSituationReportResource::collection($gasSituationReports)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $gasSituationReports->currentPage(),
                        'next_page_url' => $gasSituationReports->nextPageUrl(),
                        'prev_page_url' => $gasSituationReports->previousPageUrl(),
                        'per_page' => $gasSituationReports->perPage(),
                        'total' => $gasSituationReports->total(),
                        'last_page' => $gasSituationReports->lastPage(),
                    ]
                ])
                ->response()
                ->setStatusCode(200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * Get details of a specific Gas Situation Report record by ID.
     *
     * @OA\Get(
     *     path="/api/gas-situation-reports/{id}",
     *     tags={"Gas Situation Reports"},
     *     summary="Get details of a specific Gas Situation Report record",
     *     description="Fetches details of a specific Gas Situation Report record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Gas Situation Report record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/GasSituationReport")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     *
     * @param int $id The ID of the Gas Situation Report record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Retrieve Gas Situation Report by ID
            $gasSituationReport = $this->gasSituationReportService->getById($id);

            // Return a JsonResponse
            return (new GasSituationReportResource($gasSituationReport))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gas Situation Report record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific Gas Situation Report record by ID.
     *
     * @OA\Delete(
     *     path="/api/gas-situation-reports/{id}",
     *     tags={"Gas Situation Reports"},
     *     summary="Delete a specific Gas Situation Report record",
     *     description="Deletes a specific Gas Situation Report record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Gas Situation Report record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Gas Situation Report record deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param int $id The ID of the Gas Situation Report record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $gasSituationReport = $this->gasSituationReportService->getById($id);
            $gasSituationReport->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Gas Situation Report record deleted successfully'
            ], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gas Situation Report record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}
