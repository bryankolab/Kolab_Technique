<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\ProjectHelper;

class ProjectRestController extends Controller
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
      $this->helpers['project'] = new ProjectHelper;
    }

    /**
     * @SWG\Get(
     *      path="/project",
     *      tags={"Project"},
     *      summary="Get all projects",
     *      description="Get all projects",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     * @param Request $request
     * @return JsonResponse
     */

    public function all(Request $request)
    {
      switch ($request->input('mode')) {
        case 'search':
          $datas = $this->helpers['project']->search($request->input('term'));
          break;

        default:
          $datas = $this->helpers['project']->all($request->all());
          break;
      }

      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    /**
     * @SWG\Get(
     *      path="/project/{id}",
     *      tags={"Project"},
     *      summary="Get project detail",
     *      description="Get project detail",
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
      $datas = $this->helpers['project']->get($id);

      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    public function addOrUpdate(Request $request)
    {
      $params = $request->all();
      $header = $request->header();
      $params = array_merge($params, $header);

      $datas = $this->helpers['project']->addOrUpdate($params);

      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    //
    public function delete($id)
    {
      $datas = $this->helpers['project']->delete($id);
      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    //
    public function getPlanning($id)
    {
      $datas = $this->helpers['project']->getPlanning($id);
      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    //
    public function addFiles($id, Request $request)
    {
    	$files = $request->input('files');

    	$datas = $this->helpers['project']->addFiles($id, $files);
      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    //
    public function getFiles($id)
    {
    	$datas = $this->helpers['project']->getFiles($id);
      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    //
    public function deleteFiles(Request $request)
    {
    	$file_ids = $request->input('files');

      $datas = $this->helpers['project']->deleteFiles($file_ids);
      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

}
