<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Meta;
use App\Models\PaymentBill;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminFinanceReport extends Controller
{

    private function meta(){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Admin | Laporan Keuangan';
        return $meta;
    }

    public function index(Request $request){
        $month = $request->get('month', date('Y-m'));
        $date = Carbon::createFromFormat('Y-m', $month);

        // Pemasukan: tagihan yang sudah dibayar di bulan ini
        $income = PaymentBill::with(['house', 'feeType', 'payment'])
            ->where('status', 'paid')
            ->whereYear('billing_month', $date->year)
            ->whereMonth('billing_month', $date->month)
            ->orderBy('house_id', 'ASC')
            ->get();

        // Pengeluaran bulan ini
        $expenses = Expense::with('category')
            ->whereYear('expense_date', $date->year)
            ->whereMonth('expense_date', $date->month)
            ->orderBy('expense_date', 'DESC')
            ->get();

        $totalIncome = $income->sum('amount');
        $totalExpense = $expenses->sum('amount');

        return view('admin.finance-report',[
            "meta" => $this->meta(),
            "income" => $income,
            "expenses" => $expenses,
            "totalIncome" => $totalIncome,
            "totalExpense" => $totalExpense,
            "selisih" => $totalIncome - $totalExpense,
            "month" => $month,
        ]);
    }
}
