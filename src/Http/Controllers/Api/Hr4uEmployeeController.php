<?php

namespace Reddot\Employee\Management\Http\Controllers\Api;

use ProcessMaker\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Exception;

use Illuminate\Support\Facades\Log;

use ProcessMaker\Models\Hr4uEmployee;

use Illuminate\Support\Facades\Validator;

class Hr4uEmployeeController extends Controller
{
   
    /**
     * Display  list of employee.
     *
     * @param Request $request
     * @return ApiCollection
     *
     * /**
     * @OA\Get(
     *     path="/hr4u-employee-details",
     *     summary="Returns Hr4u employee list",
     *     operationId="hr4uEmployeeList",
     *     tags={"Hr4u Employees"},
     *     @OA\Parameter(
     *         name="division",
     *         in="query",
     *         description="Employee's Allocated division name",
     *         required=false,
     *         @OA\Schema(type="string", default=" ")
     *     ),
     *     @OA\Parameter(
     *         name="department",
     *         in="query",
     *         description="Employee's Allocated department name",
     *         required=false,
     *         @OA\Schema(type="string", default=" ")
     *     ),
     *     @OA\Parameter(
     *         name="unit",
     *         in="query",
     *         description="Employee's Allocated unit name",
     *         required=false,
     *         @OA\Schema(type="string", default=" ")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Employee's Allocated id",
     *         required=false,
     *         @OA\Schema(type="string", default=" ")
     *     ),
     *     @OA\Parameter(
     *         name="orderby",
     *         in="query",
     *         description="Employee's response direction",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc", "ASC", "DESC"}, default="desc"),
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Employee's Allocated id",
     *         required=false,
     *         @OA\Schema(type="string", default="0")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Employee's Allocated id",
     *         required=false,
     *         @OA\Schema(type="string", default="10")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="list of Employee",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/processRequest"),
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 ref="#/components/schemas/metadata",
     *             ),
     *         ),
     *     ),
     * )
     */
   
    public function hr4uEmployeeDetails(Request $request) {
        try {
            $hr4uDataValidator = Validator::make($request->all(), [
                "division" => ["nullable", "string", "max:250"],
                "department" => ["nullable", "string", "max:250"],
                "unit" => ["nullable", "string", "max:250"],
                "id" => ["nullable", "string", "max:10"],
                "orderby" => ["nullable", "in:asc,desc,ASC,DESC"],
                "offset" => ["nullable", "numeric", "gte:0"],
                "limit" => ["nullable", "numeric", "gte:1"],
            ],[
                "offset.numeric" => "The :attribute must be greater than or equal to 0.",
                "offset.gte" => "The :attribute must be greater than or equal to 0.",
                "limit.numeric" => "The :attribute must be greater than or equal to 1.",
                "limit.gte" => "The :attribute must be greater than or equal to 1.",
            ]);
            if ($hr4uDataValidator->fails()) {
                $messages = implode(",", $hr4uDataValidator->messages()->all());
                throw new Exception($messages);
            } else {
                $limit = $request->limit ?? 10;
                $offset = $request->offset ?? 0;
                $orderDirection = $request->orderby ?? "asc";
                $hr4uQuery = Hr4uEmployee::select('id', 'name', 'division', 'department', 'unit', 'designation',
                   'mobile', 'email', 'hierarchy_manager_id', 'hierarchy_manager_id', 'hierarchy_manager_name',
                   'hierarchy_manager_email', 'employee_band');
                foreach ($request->all() as $key => $value) {
                       if (in_array($key, ['id', 'division', 'department', 'unit']) && $value) {
                            $hr4uQuery->where($key, $value);
                       }
                }
                $hr4uData = $hr4uQuery->skip(round($offset))
                    ->take(round($limit))
                    ->orderBy('created_at',strtolower($orderDirection))
                    ->get();
                return response()->json(["success" => true, "hr4uData" => $hr4uData]);
            }
        } catch (Exception $e) {
            Log::error("Hr4uEmployeeController-->index" . $e->getMessage());
            return response()->json(["success" => false, "message" => $e->getMessage()]);
        }
    }
}
