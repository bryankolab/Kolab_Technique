<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;

class ProjectsController extends Controller
{
 	public function index()
 	{
 		return view('pages.tpl_projects');
 	}

 	public function details($id)
 	{
 		// Get datas
 		$project = Project::findOrFail($id);

 		return view('pages.tpl_project_details', [
 			'project' => $project
 		]);
 	}

}
