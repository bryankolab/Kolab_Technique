<?php

namespace App\Http\Helpers;


use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

use App\User;
use Spatie\Permission\Models\Role;

use App\Http\Helpers\NotificationHelper;

/**
 * Class TalentHelper
 * @package App\Http\Helpers
 */
class TalentHelper
{
    /**
     * Retrieve all talents
     *
     * @param $request
     * @return \Illuminate\Support\Collection
     */
    public function all($request = [])
    {
    	$users = User::role('talent');

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_is_explorer', $request)) {
            $users->where('is_explorer', '=', $request['filter_is_explorer']);
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_alpha', $request)) {
            $users->where('firstname', 'LIKE', $request['filter_alpha'] . '%');
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_name', $request)) {
            $users->where('name', 'LIKE', '%' . $request['filter_name'] . '%');
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_jobs', $request)) {
            $users->whereIn('job_id', $request['filter_jobs']);
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_skills', $request)) {
            $users->whereHas('skills', function ($query) use ($request) {
                $query->whereIn('id', $request['filter_skills']);
            });
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_date', $request)) {
        	$users->whereDoesntHave('tasks', function($query) use ($request) {
					  $query->where('start_date', '<', $request['filter_date'])
					  			->where('end_date', '>=', $request['filter_date']);
					});
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('itemsPerPage', $request)) {
            $users->limit($request['itemsPerPage']);
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('page', $request)) {
            $users->skip(($request['page'] - 1) * $request['page']);
        }

        if(_helper('api')->checkParameterExistAndNotEmpty('term', $request)) {
            return User::where('firstname', 'LIKE', '%' . $request['term'] . '%')
            					->orWhere('lastname', 'LIKE', '%' . $request['term'] . '%')
            					->get();
        }

        if(!empty($_GET['exclude'])){
        	$users->where('id', '!=', $_GET['exclude']);
        }

    		return $users->orderBy('firstname', 'ASC')->get();
    }

    /**
     * Retrieve one Talent by its id
     *
     * @param int $id
     * @return User|User[]|Collection|Model|null
     */
    public function get(int $id): ?User
    {
    	return User::find($id);
    }

    /**
     * Create or update a Talent
     *
     * @param [] $params
     * @return \Illuminate\Support\Collection
     */
    public function addOrUpdate($params)
    {
    	$this->helpers['notification'] = new NotificationHelper;

      $talent = $params['talent'];
      $mandatoryParameters = ['email', 'firstname', 'lastname'];

      // Check if all parameters are provided
      try {
          _helper('api')->checkParameters($talent, $mandatoryParameters);
      } catch (Exception $e) {
          // Return the exception message if error
  				_helper('api')->error($e->getMessage());
  		}

    	if (array_key_exists('id', $talent) && $talent['id']) {
    		$action = 'UPDATE';
    		$user = User::find($talent['id']);
    		$user->updated_by = (int)$params['user'][0];
    	} else {
    		$action = 'ADD';
    		$user = new User();
    		$user->password = '$UJVSy2H_H2ySVJU$'; // Tmp password for user creation
    		$user->created_by = (int)$params['user'][0];
    		$user->updated_by = (int)$params['user'][0];
    	}

    	$user->name = ucFirst($talent['firstname']) .' '. ucFirst($talent['lastname']);
    	$user->email = $talent['email'];
    	$user->firstname = ucFirst($talent['firstname']);
    	$user->lastname = ucFirst($talent['lastname']);
    	$user->phone = $talent['phone'];
    	$user->price = $talent['price'];
    	$user->city = ucFirst($talent['city']);
    	$user->country = ucFirst($talent['country']);
    	$user->profile_type = $talent['profile_type'];
    	$user->profile_url = $talent['profile_url'];
    	$user->instagram_url = $talent['instagram_url'];
    	$user->vimeo_url = $talent['vimeo_url'];
    	$user->youtube_url = $talent['youtube_url'];
    	$user->job_id = $talent['job_id'];

      // First save to make the link to skills and role possible
      try {
          $user->save();
      } catch (Exception $e) {
          _helper('api')->error($e->getMessage());
      }

      Role::findOrCreate('talent');
      $user->assignRole('talent');

      $user->skills()->detach();
      if (array_key_exists('skills_ids', $talent) && is_array($talent['skills_ids'])) {
          $user->skills()->sync($talent['skills_ids']);
      }

      try {
          $user->save();

          $icon = $action == 'ADD' ? '🆕' : '✅';
          $action = $action == 'ADD' ? 'ajouté' : 'modifié';
	        $this->helpers['notification']->save("$icon Le talent ".$user->name." a bien été ".$action."", $params['user'][0], 'TALENT', $user->id);
      } catch (Exception $e) {
          _helper('api')->error($e->getMessage());
      }

      return $user;
    }

    /**
     * Update profile
     *
     * @param [] $params
     * @return \Illuminate\Support\Collection
     */
    public function updateProfile($params)
    {
    	if (array_key_exists('user_id', $params) && $params['user_id']) {
    		$action = 'UPDATE';
    		$user = User::find($params['user_id']);
    		$user->firstname = $params['firstname'];
    		$user->lastname = $params['lastname'];
    		$user->name = $user->firstname . ' ' . $user->lastname;

    		if(!empty($params['password'])){
    			$user->password = Hash::make($params['password']);
    		}

    		$user->save();
    	} else {
    		return false;
    	}

    	return $user;
    }

    /**
     * Delete One Talent
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
    	$this->helpers['notification'] = new NotificationHelper;

      $user = User::find($id);
      try {
      		$name = $user->name;
          $user->skills()->detach();
          $user->projects()->detach();
          $user->tasks()->detach();
          $user->delete();

          $this->helpers['notification']->save("🗑 Le talent ".$name." a bien été supprimé", null, 'TALENT', $id);
      } catch (Exception $e) {
          _helper('api')->error($e->getMessage());
      }

      return true;
    }


    public function search($term = null)
    {
        if ($term) {
            return User::where('firstname', 'like', '%' . $term . '%')->orWhere('lastname', 'like', '%' . $term . '%')->get();
        } else {
            return User::all();
        }
    }
}
