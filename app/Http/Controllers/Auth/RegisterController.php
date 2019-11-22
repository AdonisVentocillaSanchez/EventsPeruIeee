<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\UserType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        
        $userExist = User::where('email','=', $data['email'])->first();
        
        
        if ($userExist !== null) {
            return $userExist;
        }
        

        // if (explode("@", $data['email'])[1] !== 'ieee.org') {
        //     return redirect('/login');
        // }

       
        DB::beginTransaction();
        try {
            
            $person = Person::create([
                'firstName' => $data['firstname'],
                'middleName' => '',
                'lastName' => $data['lastname'],
                'email_verified_at' => null,
                'status' => 1,//Activo
                'institute_id' => null,
                'document_id' => null,
                'phone_id' => null,
            ]);
    
                
            $u = User::create([
                'nickname' => $data['firstname']." ".$data['lastname'],
                'person_id' => $person->id,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            
    
            UserType::create([
                'user_id' => $u->id,
                'role_id' => 1, //General
            ]);

            session()->put('userId', $u->id);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        DB::commit();
        
        return $u;
    }
}
