<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Meta;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class AdminExpenseCategory extends Controller
{

    private function meta(){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Admin | Kategori Pengeluaran';
        return $meta;
    }

    public function index(){
        return view('admin.expense-category',[
            "meta" => $this->meta(),
            "categories" => ExpenseCategory::orderBy("name","ASC")->get(),
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
            'is_recurring'=>'nullable',
        ]);

        $validatedData['is_recurring'] = $request->has('is_recurring') ? true : false;

        ExpenseCategory::create($validatedData);
        return ['status'=>'success','message'=>'Kategori pengeluaran berhasil ditambahkan'];
    }

    public function update(Request $request){
        $validatedData = $request->validate([
            'id'=>'required|numeric',
            'name'=>'required',
            'is_recurring'=>'nullable',
        ]);

        $category = ExpenseCategory::find($request->id);

        if(!$category){
            return ['status'=>'error','message'=>'Data tidak ditemukan'];
        }

        $validatedData['is_recurring'] = $request->has('is_recurring') ? true : false;

        $category->update($validatedData);
        return ['status'=>'success','message'=>'Kategori pengeluaran berhasil diedit'];
    }

    public function destroy(Request $request){
        $validatedData = $request->validate([
            'id' => 'required|numeric',
        ]);

        $category = ExpenseCategory::find($request->id);

        if (!$category) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        if($category->expenses()->count() > 0){
            return ['status' => 'error', 'message' => 'Tidak dapat menghapus kategori yang sudah memiliki pengeluaran'];
        }

        $category->delete();
        return ['status' => 'success', 'message' => 'Kategori pengeluaran berhasil dihapus'];
    }
}
