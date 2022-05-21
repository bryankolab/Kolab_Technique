<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UserHelper;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Response;

class UserRestController extends Controller
{

    public function passwordChange(Request $request)
    {
        $input = $request->all();
        $userid = Auth::user()->id;
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = Response([
                "message" => $validator->errors()->first(),
                "data" => []
            ], 400);
        } else {
            try {
                if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
                    $response = Response([
                        "message" => "Check your old password.",
                        "data" => []
                    ], 400);

                } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                    $response = Response([
                        "message" => "Please enter a password which is not similar then current password.",
                        "data" => []
                    ], 400);
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);

                    $response = Response([
                        "message" => "Password updated successfully.",
                        "data" => []
                    ], 200);
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $response = Response([
                    "message" => $msg,
                    "data" => []
                ], 400);
            }
        }
        return $response;
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        try {
            $updatedUser = $request->all()['user'];
            $user->update($updatedUser);

            $user->name = $user->firstname . ' ' . $user->lastname;

            if (array_key_exists('skills_ids', $updatedUser)) {
                $user->skills()->sync($updatedUser['skills_ids']);
            }

            $user->save();
            return $user;
        } catch (\Exception $e) {
            return Response($e->getMessage(), 500);
        }
    }

    public function updatePicture(Request $request)
    {
        $data = array();

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:png,jpg,jpeg|max:2048'
        ]);

        if ($validator->fails()) {
            $data['success'] = 0;
            $data['error'] = $validator->errors()->first('file');// Error response
        } else {
            if ($request->file('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();

                // File extension
                $extension = $file->getClientOriginalExtension();

                // File upload location
                $location = 'upload/avatars';

                // Upload file
                $file->move($location, $filename);

                // File path
                $filepath = url('upload/avatars/' . $filename);

                $user = Auth::user();
                $user->avatar = $filename;
                $user->save();

                // Response
                $data['success'] = 1;
                $data['message'] = 'Uploaded Successfully!';
                $data['filepath'] = $filepath;
                $data['extension'] = $extension;
            } else {
                // Response
                $data['success'] = 2;
                $data['message'] = 'File not uploaded.';
            }
        }

        return response()->json($data);
    }
}
