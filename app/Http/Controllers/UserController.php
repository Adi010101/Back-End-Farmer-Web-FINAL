<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('verified', 'false')->get();
        return response()->json([
            'status'=>200,
            'users'=>$users,
        ]);

    }

    public function index2()
    {
        $users2 = User::where('verified', 'true')->get();
        return response()->json([
            'status'=>200,
            'users2'=>$users2,
        ]);
    }

    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'firstname'=>'required',
            'middlename'=>'required',
            'lastname'=>'required',
            'birthdate'=>'nullable',
            'gender'=>'nullable',
            'username'=>'required',
            'mobilephone'=>'required|max:11',
            'email'=>'required',
            'image'=>'required|image|mimes:jpeg,png,jpg|max:2048',
            'userImage'=>'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'brgy'=>'nullable',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> 422,
                'errors'=> $validator->messages(),
            ]);
        }
        else
        {
            $user = new User;
            $user->firstname=$req->input('firstname');
            $user->middlename=$req->input('middlename');
            $user->lastname=$req->input('lastname');
            $user->birthdate=$req->input('birthdate');
            $user->gender=$req->input('gender');
            $user->username=$req->input('username');
            $user->mobilephone=$req->input('mobilephone');
            $user->email=$req->input('email');
            $user->brgy=$req->input('brgy');

            if($req->hasFile('image'))
            {
                $file = $req->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('uploads/user/',$filename);
                $user->image = 'uploads/user/'.$filename;
            }

            $user->verified=$req->input('verified');
            $user->brgy=$req->input('brgy');
            $user->password=Hash::make($req->input('password'));
            $user->save();

            return response()->json([
                'status'=> 200,
                'message' => 'Registration Submitted',
            ]);
        }

    }

    //
    function login(Request $req)
    {
        $user= User::where('username',$req->username)->first();
        if(!$user || !Hash::check($req->password, $user->password))
        {
            return ["error"=> "Email or password is incorrect"];
        }
        if($user && Hash::check($req->password,$user->password) && !($user->verified == "true")){
            return ["notVerified"=>"User is not yet verified"];
        }
        return $user;
    }

    public function edit($id)
    {
        $user = User::find($id);
        if($user)
        {
            return response()->json([
                'status'=> 200,
                'user' => $user,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No User ID Found',
            ]);
        }

    }

    public function update(Request $req, $id)
    {
        $validator = Validator::make($req->all(),[
            'firstname'=>'required',
            'middlename'=>'required',
            'lastname'=>'required',
            'birthdate'=>'required',
            'gender'=>'required',
            'username'=>'required',
            'mobilephone'=>'required|max:11',
            'email'=>'required',
            'brgy'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> 422,
                'validationErrors'=> $validator->messages(),
            ]);
        }
        else
        {
            $user = User::find($id);
            if($user)
            {
                $user->firstname=$req->input('firstname');
                $user->middlename=$req->input('middlename');
                $user->lastname=$req->input('lastname');
                $user->birthdate=$req->input('birthdate');
                $user->gender=$req->input('gender');
                $user->username=$req->input('username');
                $user->mobilephone=$req->input('mobilephone');
                $user->email=$req->input('email');
                $user->brgy=$req->input('brgy');
                $user->update();

                return response()->json([
                    'status'=> 200,
                    'message'=>'User Account Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'message' => 'No User ID Found',
                ]);
            }
        }
    }

    public function editPassword($id)
    {
        $user = User::find($id);
        if($user)
        {
            return response()->json([
                'status'=> 200,
                'user' => $user,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No User ID Found',
            ]);
        }

    }

    public function updatePassword(Request $req, $id)
    {
        $validator = Validator::make($req->all(),[
            'password'=>'nullable|min:8',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> 422,
                'validationErrors'=> $validator->messages(),
            ]);
        }
        else
        {
            $user = User::find($id);
            if($user)
            {
                $user->password=Hash::make($req->input('password'));
                $user->update();

                return response()->json([
                    'status'=> 200,
                    'message'=>'User Account Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'message' => 'No User ID Found',
                ]);
            }
        }
    }

    public function getUserInfo(Request $request, $id)
    {
        $user = User::where('id', '=', $id)->get();

        return $user;
    }

    public function editVerification($id)
    {
        $user = User::find($id);
        if($user)
        {
            return response()->json([
                'status'=> 200,
                'user' => $user,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No User ID Found',
            ]);
        }

    }

    public function verification(Request $request, $id)
    {
        $verified = $request->get('verified');

        $user = User::find($id);
        $user->verified = $verified;
        $user->update();

        return response()->json([
            'status'=> 200,
            'message'=>'Seller Status Updated and Ready to Start Selling!',
        ]);
    
    }

    public function editImage($id)
    {
        $user = User::find($id);
        if($user)
        {
            return response()->json([
                'status'=> 200,
                'user' => $user,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No User ID Found',
            ]);
        }

    }

    public function updateImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'userImage'=>'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> 422,
                'errors'=> $validator->messages(),
            ]);
        }
        else
        {
            $user = User::find($id);
            if($user)
            {
                if($request->hasFile('userImage'))
                {
                    $files = $request->file('userImage');
                    $extension = $files->getClientOriginalExtension();
                    $filename = time().'.'.$extension;
                    $files->move('uploads/userImage/',$filename);
                    $user->userImage = 'uploads/userImage/'.$filename;
                    $user->update();

                    return response()->json([
                        'status'=> 200,
                        'message'=>'User Account Updated Successfully',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'message' => 'No User ID Found',
                ]);
            }
        }
    }
}
