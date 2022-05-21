<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\TaskHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskRestController extends Controller
{
    /**
     * Helpers
     * @var [Array]
     */
    private $helpers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->helpers['api'] = new ApiHelper;
        $this->helpers['task'] = new TaskHelper();
    }

    /**
     * @SWG\Get(
     *      path="/task",
     *      tags={"Task"},
     *      summary="Get all tasks",
     *      description="Get all tasks",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     * @param Request $request
     * @return JsonResponse
     */

    public function all(Request $request)
    {
        $datas = $this->helpers['task']->all($request->all());

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Get(
     *      path="/task/{id}",
     *      tags={"Task"},
     *      summary="Get task detail",
     *      description="Get task detail",
     *      @SWG\Parameter(
     *          name="id",
     *          description="",
     *          required=true,
     *          type="string",
     *          in="path"
     *      ),
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     */

    public function get($id)
    {
        $datas = $this->helpers['task']->get($id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    //
    public function addOrUpdate(Request $request)
    {
        $params = $request->all();
        $header = $request->header();
        $params = array_merge($params, $header);

        $datas = $this->helpers['task']->addOrUpdate($params);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    //
    public function delete($id)
    {
        $datas = $this->helpers['task']->delete($id);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    //
    public function patch($id, Request $request)
    {
        $datas = $this->helpers['task']->patch($id, $request->all());
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}
