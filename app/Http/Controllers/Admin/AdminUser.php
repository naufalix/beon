<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Meta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class AdminUser extends Controller
{

    private function meta(){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Admin | Pegawai';
        return $meta;
    }

    public function index(){
        return view('admin.user',[
            "meta" => $this->meta(),
            "users" => User::orderBy("name","ASC")->get(),
        ]);
    }

    public function postHandler(Request $request){
        //dd($request);
        if($request->submit=="store"){
            $res = $this->store($request);
            return back()->with($res['status'],$res['message']);
        }
        if($request->submit=="update"){
            $res = $this->update($request);
            return back()->with($res['status'],$res['message']);
        }
        if($request->submit=="destroy"){
            $res = $this->destroy($request);
            return back()->with($res['status'],$res['message']);
            // return back()->with("info","Fitur hapus sementara dinonaktifkan");
        }
    }

    public function store(Request $request){
        
        $validatedData = $request->validate([
            'name'=>'required',
            'email'=>'required',
            'password'=>'required',
            'position'=>'required',
        ]);
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Check Email
        if(User::whereEmail($request->email)->first()){
            return ['status'=>'error','message'=>'Email telah terpakai'];
        }
        
        User::create($validatedData);
        return ['status'=>'success','message'=>'Pegawai berhasil ditambahkan'];

    }

    public function update(Request $request){
        $validatedData = $request->validate([
            'id'=>'required|numeric',
            'name'=>'required',
            'email'=>'required',
            'position'=>'required',
        ]);
        
        $user = User::find($request->id);
 
        //Check if the data is found
        if(!$user){
            return ['status'=>'error','message'=>'Data tidak ditemukan'];
        }

        //Check password
        if($request->password){
            $validatedData['password'] = Hash::make($request->password);
        }else{
            $validatedData['password'] = $user->password;
        }

        // Check Email
        if ($request->email !== $user->email && User::where('email', $request->email)->exists()) {
            return ['status' => 'error', 'message' => 'Email telah terpakai'];
        }

        // Update data
        $user->update($validatedData);    
        return ['status'=>'success','message'=>'Pegawai berhasil diedit'];
    }

    public function destroy(Request $request){
        
        $validatedData = $request->validate([
            'id' => 'required|numeric',
        ]);
        
        $user = User::find($request->id);
        
        // Check if the data is found
        if (!$user) {
            return ['status' => 'error', 'message' => 'Pegawai tidak ditemukan'];
        }
        
        $user->delete();
        return ['status' => 'success', 'message' => 'Pegawai berhasil dihapus'];
    }

}
