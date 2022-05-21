<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\ProjectFile;

class AdminController extends Controller
{

 	/**
   * [generateFile description]
   * @param  [type] $id     [description]
   * @param  string $action [description]
   * @return [type]         [description]
   */
  public function generateFile($id, $action = 'view')
  {
  	$file = ProjectFile::where('uniqid', $id)->first();

  	if (!empty($file) && file_exists($file->path)){

  		if($action == 'view'){
  			return response()->file($file->path);
  		}

  		if($action == 'download'){
      	return response()->download($file->path);
    	}
  	}

  	response()->json('Accès non autorisé')->send();
  }

}
