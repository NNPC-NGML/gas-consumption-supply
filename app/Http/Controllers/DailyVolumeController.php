<?php

namespace App\Http\Controllers;

use App\Http\Resources\DailyVolumeResource;
use App\Services\DailyVolumeService;
use Illuminate\Http\Request;

class DailyVolumeController extends Controller
{
    /**
     * The DailyVolumeService instance.
     *
     * @var DailyVolumeService
     */
    protected DailyVolumeService $dailyVolumeService;

    /**
     * DailyVolumeController constructor.
     *
     * @param DailyVolumeService $dailyVolumeService
     */
    public function __construct(DailyVolumeService $dailyVolumeService)
    {
        $this->dailyVolumeService = $dailyVolumeService;
    }

    /**
     * @OA\Get(
     *     path="/api/daily-volumes",
     *     tags={"Daily Volumes"},
     *     summary="Get a list of daily volumes with filters and pagination",
     *     description="Fetches a list of daily volumes, with optional filtering based on fields in the daily volumes table, and supports pagination.",
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
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/daily-volumes?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/DailyVolume")
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
            $dailyVolumes = $this->dailyVolumeService->getAllWithFilters($filters, $per_page);

            // Return the paginated results with additional pagination metadata
            return DailyVolumeResource::collection($dailyVolumes)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $dailyVolumes->currentPage(),
                        'next_page_url' => $dailyVolumes->nextPageUrl(),
                        'prev_page_url' => $dailyVolumes->previousPageUrl(),
                        'per_page' => $dailyVolumes->perPage(),
                        'total' => $dailyVolumes->total(),
                        'last_page' => $dailyVolumes->lastPage(),
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
     * Get details of a specific Daily Volume record by ID.
     *
     * @OA\Get(
     *     path="/api/daily-volumes/{id}",
     *     tags={"Daily Volumes"},
     *     summary="Get details of a specific Daily Volume record",
     *     description="Fetches details of a specific Daily Volume record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Daily Volume record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/DailyVolume")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     *
     * @param int $id The ID of the Daily Volume record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Retrieve Daily Volume by ID
            $dailyVolume = $this->dailyVolumeService->getById($id);

            // Return a JsonResponse
            return (new DailyVolumeResource($dailyVolume))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Daily Volume record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific Daily Volume record by ID.
     *
     * @OA\Delete(
     *     path="/api/daily-volumes/{id}",
     *     tags={"Daily Volumes"},
     *     summary="Delete a specific Daily Volume record",
     *     description="Deletes a specific Daily Volume record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Daily Volume record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Daily Volume record deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param int $id The ID of the Daily Volume record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $dailyVolume = $this->dailyVolumeService->getById($id);
            $dailyVolume->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Daily Volume record deleted successfully'
            ], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Daily Volume record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}
