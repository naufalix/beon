<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Meta;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminExpense extends Controller
{

    private function meta(){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Admin | Pengeluaran';
        return $meta;
    }

    public function index(Request $request){
        $month = $request->get('month', date('Y-m'));
        $date = Carbon::createFromFormat('Y-m', $month);

        $expenses = Expense::with('category')
            ->whereYear('expense_date', $date->year)
            ->whereMonth('expense_date', $date->month)
            ->orderBy('expense_date', 'DESC')
            ->get();

        return view('admin.expense',[
            "meta" => $this->meta(),
            "expenses" => $expenses,
            "categories" => ExpenseCategory::orderBy('name','ASC')->get(),
            "month" => $month,
            "totalExpense" => $expenses->sum('amount'),
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
            'category_id'=>'required|numeric|exists:expense_categories,id',
            'amount'=>'required|numeric|min:0',
            'expense_date'=>'required|date',
            'description'=>'nullable',
        ]);

        Expense::create($validatedData);
        return ['status'=>'success','message'=>'Pengeluaran berhasil ditambahkan'];
    }

    public function update(Request $request){
        $validatedData = $request->validate([
            'id'=>'required|numeric',
            'category_id'=>'required|numeric|exists:expense_categories,id',
            'amount'=>'required|numeric|min:0',
            'expense_date'=>'required|date',
            'description'=>'nullable',
        ]);

        $expense = Expense::find($request->id);

        if(!$expense){
            return ['status'=>'error','message'=>'Data tidak ditemukan'];
        }

        $expense->update($validatedData);
        return ['status'=>'success','message'=>'Pengeluaran berhasil diedit'];
    }

    public function destroy(Request $request){
        $validatedData = $request->validate([
            'id' => 'required|numeric',
        ]);

        $expense = Expense::find($request->id);

        if (!$expense) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        $expense->delete();
        return ['status' => 'success', 'message' => 'Pengeluaran berhasil dihapus'];
    }
}
