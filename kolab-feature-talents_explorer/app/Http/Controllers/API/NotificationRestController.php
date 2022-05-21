<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\NotificationHelper;

class NotificationRestController extends Controller
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
        $this->helpers['notification'] = new NotificationHelper();
    }

    //
    public function save(Request $request)
    {
    		$message = $request->input('message');
    		$user_id = $request->input('user_id');
    		$element_type = $request->input('element_type');
    		$element_id = $request->input('element_id');

    		$datas = $this->helpers['notification']->save($message, $user_id, $element_type, $element_id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    //
    public function get()
    {
        $datas = $this->helpers['notification']->get();

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}
