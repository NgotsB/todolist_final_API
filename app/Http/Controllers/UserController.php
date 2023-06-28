<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{
    /**
     * Get Users
     * 
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $key = $request->q;
        $deleted = $request->deleted;
        $query = User::where('name', 'LIKE', '%' . $key . '%');
        

        $query->where('deleted', '=', $deleted ?? '0')
            ->where('id', '!=', Auth::user()->id);

        $users = $query->latest()->paginate($request->per_age ?? 15);

        if (empty($users)) {
            return response([
                'message' => 'Users not found.',
            ], 404);
        }

        return response([
            'message' => 'Users have been retrieved.',
            'data' => [
                'users' => $users
            ],
            'success' => true
        ], 200);
    }

    /**
     * Get User Details by Id
     * 
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return response([
            'message' => 'User retreived.',
            'data' => [
                'user' => $user,
            ]
        ], 200);
    }


    /**
     * Insert new user
     * 
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validate = Validator::make($input, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()->first(),
                'success' => false,
            ], 400);
        }

        $input['password'] = bcrypt($input['password']);
        $input['username'] = $input['email'];

        $image = $request->file('picture');

        if ($image) {
            $imageName = Str::orderedUuid() . ".". $image->getClientOriginalExtension();
            $filePath = 'images/' . $imageName;
            $disk = Storage::disk('public')->put($filePath, file_get_contents($image));
            $public = Storage::disk('public');
            $url = $public->url($filePath);
            $input['picture'] = $url;
        }

        $user = User::create($input);


        return response([
            'message' => 'User created.',
            'success' => true,
            'data' => [
                'user' => User::find($user->id)
            ]
        ], 200);
    }

    /**
     * Patch User
     *
     * @param Request $request
     * @param String $id
     * @return Response
     */
    public function patch(Request $request, $id)
    {
        if (!$id) {
            return response([
                'message' => 'User not found.',
                'success' => false
            ], 404);
        }

        $user = User::find($id);

        if (!$user) {
            return response([
                'message' => 'User not found.',
                'success' => false
            ], 404);
        }

        $keys = collect($request->all())->keys();

        for ($i = 0; $i < count($keys); $i++) {
            $field = $keys[$i];
            $user->$field = $request->$field;
        }

        $user->save();


        return response([
            'message' => 'User updated.',
            'data' => [
                'user' => $user,
            ]
        ], 200);
    }

    /**
     * Change Password the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'new_password'  => 'required|different:password',
            'confirm_password' => 'same:new_password',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => $validate->errors()->first(),
                'success' => false,
            ], 400);
        }

        $user = User::find(auth()->user()->id);

        if (Hash::check($request->password, $user->password)) {
            $user->password = bcrypt($request->new_password);
            $user->save();

            return response([
                'message' => 'User password successfully changed!',
                'success' => true
            ], 200);

            return ResponseHelper::success([], 'User password successfully changed!');
        } else {
            return response([
                'message' => 'Wrong password!',
                'success' => false
            ], 400);
        }
        
    }

}
