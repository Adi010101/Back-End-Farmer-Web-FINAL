<?php

namespace App\Http\Controllers\API;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    //
    function registerCustomer(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'firstname'=>'required|max:50',
            'middlename'=>'required|max:50',
            'lastname'=>'required|max:50',
            'username'=>'required|max:50',
            'mobilephone'=>'nullable',
            'email'=>'required',
            'password'=>'required',
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
            $customer = new Customer;
            $customer->firstname=$req->input('firstname');
            $customer->middlename=$req->input('middlename');
            $customer->lastname=$req->input('lastname');
            $customer->username=$req->input('username');
            $customer->mobilephone=$req->input('mobilephone');
            $customer->email=$req->input('email');
            $customer->password=Hash::make($req->input('password'));
            $customer->save();

            return response()->json([
                'status'=> 200,
                'message' => 'Customer Registered Successfully.',
            ]);
        }

        
    }

    //
    function loginCustomer(Request $req)
    {
        $customer= Customer::where('username',$req->username)->first();
        if(!$customer || !Hash::check($req->password, $customer->password))
        {
            return ["error"=> "Email or password is incorrect"];
        }
        return $customer;
    }

    public function editCustomer($id)
    {
        $customer = Customer::find($id);
        if($customer)
        {
            return response()->json([
                'status'=> 200,
                'customer' => $customer,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No Customer ID Found',
            ]);
        }

    }

    public function updateCustomer(Request $req, $id)
    {
        $validator = Validator::make($req->all(),[
            'firstname'=>'nullable',
            'middlename'=>'nullable',
            'lastname'=>'nullable',
            'mobilephone'=>'required|max:11',
            'email'=>'nullable',
            'password'=>'nullable|min:8',
            'address'=>'required',
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
            $customer = Customer::find($id);
            if($customer)
            {
                $customer->firstname=$req->input('firstname');
                $customer->middlename=$req->input('middlename');
                $customer->lastname=$req->input('lastname');
                $customer->mobilephone=$req->input('mobilephone');
                $customer->email=$req->input('email');
                $customer->password=Hash::make($req->input('password'));
                $customer->address=$req->input('address');
                $customer->update();

                return response()->json([
                    'status'=> 200,
                    'message'=>'Customer Account Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'message' => 'No Customer ID Found',
                ]);
            }
        }
    }

    public function getCustomerCart(Request $request, $id)
    {
        $customer = Customer::where('user_id', '=', $id)->get();

        return $customer;
    }
}