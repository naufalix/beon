<?php

namespace App\Http\Controllers\Auth;
use App\Models\Meta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    private function meta(){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Login Pegawai';
        return $meta;
    }

    public function index(){
        
        if (Auth::guard('user')->check()) {
            return redirect('/dashboard');
        }

        return view('dashboard.login',[
            "meta" => $this->meta(),
        ]);
    }

    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if(Auth::guard('user')->attempt($credentials)){
            // $status = Auth::guard('user')->user()->status;
            // if($status=="nonactive"){
            //     return back()->with('error','Akun anda dinonaktifkan!');
            // }
            $request->session()->regenerate();
            return redirect()->intended('/dashboard/home');
        }

        // return view('admin.login',[
        //     "meta" => $this->meta(),
        // ]);

        return back()->with('error','Invalid email or password!');
    }

    public function logout(){
        if(Auth::guard('user')->check()){
            Auth::guard('user')->logout();
        }
        return redirect('/login');
    }
}
