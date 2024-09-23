<?php

namespace App\Http\Controllers;

use App\Http\Resources\GasCostResource;
use App\Services\GasCostService;
use Illuminate\Http\Request;

class GasCostController extends Controller
{
    /**
     * The GasCostService instance.
     *
     * @var GasCostService
     */
    protected GasCostService $gasCostService;

    /**
     * GasCostController constructor.
     *
     * @param GasCostService $gasCostService
     */
    public function __construct(GasCostService $gasCostService)
    {
        $this->gasCostService = $gasCostService;
    }

    /**
     * @OA\Get(
     *     path="/api/gas-costs",
     *     tags={"Gas Costs"},
     *     summary="Get a list of gas costs with filters and pagination",
     *     description="Fetches a list of gas costs, with optional filtering based on fields in the gas costs table, and supports pagination.",
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/gas-costs?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/GasCost")
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
            $filters = $request->all();
            $per_page = $request->input('per_page', default: 50);

            $gasCosts = $this->gasCostService->getAllWithFilters($filters, $per_page);

            return GasCostResource::collection($gasCosts)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $gasCosts->currentPage(),
                        'next_page_url' => $gasCosts->nextPageUrl(),
                        'prev_page_url' => $gasCosts->previousPageUrl(),
                        'per_page' => $gasCosts->perPage(),
                        'total' => $gasCosts->total(),
                        'last_page' => $gasCosts->lastPage(),
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
     * Get details of a specific GasCost record by ID.
     *
     * @OA\Get(
     *     path="/api/gas-costs/{id}",
     *     tags={"Gas Costs"},
     *     summary="Get details of a specific GasCost record",
     *     description="Fetches details of a specific GasCost record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the GasCost record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/GasCost")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     *
     * @param int $id The ID of the GasCost record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $gasCost = $this->gasCostService->getById($id);

            return (new GasCostResource($gasCost))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gas Cost record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific GasCost record by ID.
     *
     * @OA\Delete(
     *     path="/api/gas-costs/{id}",
     *     tags={"Gas Costs"},
     *     summary="Delete a specific GasCost record",
     *     description="Deletes a specific GasCost record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the GasCost record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Gas Cost record deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param int $id The ID of the GasCost record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $gasCost = $this->gasCostService->getById($id);
            $gasCost->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Gas Cost record deleted successfully'
            ], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gas Cost record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}
