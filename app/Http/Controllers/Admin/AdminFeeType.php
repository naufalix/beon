<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Meta;
use App\Models\FeeType;
use Illuminate\Http\Request;

class AdminFeeType extends Controller
{

    private function meta(){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Admin | Jenis Iuran';
        return $meta;
    }

    public function index(){
        return view('admin.fee-type',[
            "meta" => $this->meta(),
            "feeTypes" => FeeType::orderBy("name","ASC")->get(),
        ]);
    }

    public function postHandler(Request $request){
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
        }
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'name'=>'required',
            'amount'=>'required|numeric|min:0',
            'is_active'=>'nullable',
        ]);

        $validatedData['is_active'] = $request->has('is_active') ? true : false;

        FeeType::create($validatedData);
        return ['status'=>'success','message'=>'Jenis iuran berhasil ditambahkan'];
    }

    public function update(Request $request){
        $validatedData = $request->validate([
            'id'=>'required|numeric',
            'name'=>'required',
            'amount'=>'required|numeric|min:0',
            'is_active'=>'nullable',
        ]);

        $feeType = FeeType::find($request->id);

        if(!$feeType){
            return ['status'=>'error','message'=>'Data tidak ditemukan'];
        }

        $validatedData['is_active'] = $request->has('is_active') ? true : false;

        $feeType->update($validatedData);
        return ['status'=>'success','message'=>'Jenis iuran berhasil diedit'];
    }

    public function destroy(Request $request){
        $validatedData = $request->validate([
            'id' => 'required|numeric',
        ]);

        $feeType = FeeType::find($request->id);

        if (!$feeType) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        if($feeType->paymentBills()->count() > 0){
            return ['status' => 'error', 'message' => 'Tidak dapat menghapus jenis iuran yang sudah memiliki tagihan'];
        }

        $feeType->delete();
        return ['status' => 'success', 'message' => 'Jenis iuran berhasil dihapus'];
    }
}
