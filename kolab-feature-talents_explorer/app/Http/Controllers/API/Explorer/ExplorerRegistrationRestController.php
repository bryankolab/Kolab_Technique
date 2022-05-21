<?php

namespace App\Http\Controllers\API\Explorer;

use App\Http\Controllers\Controller;
use App\Http\Services\Explorer\ExplorerRegistrationService;
use Illuminate\Http\Request;
use function response;

class ExplorerRegistrationRestController extends Controller
{
    //
    public function create(Request $request, ExplorerRegistrationService $explorerRegistrationService)
    {
        $user = \Auth::user();

        try {
            return $explorerRegistrationService->register($user, $request->all());
        } catch (\Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }

    public function unlockAccess(Request $request, ExplorerRegistrationService $explorerRegistrationService)
    {
        $user = \Auth::user();

        $requestParams = $request->all()['params'];
        try {
            $explorerRegistrationService->unlockExplorer($user, $requestParams['access_code']);
            return response(true, 200);
        } catch (\Exception $exception) {
            return response($exception->getMessage(), 403);
        }
    }
}
