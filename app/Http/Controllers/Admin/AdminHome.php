<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Meta;
use App\Models\PaymentBill;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminHome extends Controller
{

    private function meta(){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Admin | Home';
        return $meta;
    }

    public function index(){
        $now = Carbon::now();

        // Saldo Kas = Total pemasukan (all time) - Total pengeluaran (all time)
        $totalIncome = PaymentBill::where('status', 'paid')->sum('amount');
        $totalExpense = Expense::sum('amount');
        $saldoKas = $totalIncome - $totalExpense;

        // Pemasukan Bulan Ini
        $incomeThisMonth = PaymentBill::where('status', 'paid')
            ->whereYear('billing_month', $now->year)
            ->whereMonth('billing_month', $now->month)
            ->sum('amount');

        // Pengeluaran Bulan Ini
        $expenseThisMonth = Expense::whereYear('expense_date', $now->year)
            ->whereMonth('expense_date', $now->month)
            ->sum('amount');

        // Tagihan Belum Lunas
        $unpaidCount = PaymentBill::where('status', 'unpaid')->count();

        // Grafik 12 bulan terakhir
        $startDate = $now->copy()->subMonths(11)->startOfMonth();

        // Pemasukan per bulan (MySQL)
        $incomePerMonth = PaymentBill::where('status', 'paid')
            ->where('billing_month', '>=', $startDate->format('Y-m-d'))
            ->selectRaw("DATE_FORMAT(billing_month, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Pengeluaran per bulan (MySQL)
        $expensePerMonth = Expense::where('expense_date', '>=', $startDate->format('Y-m-d'))
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Build 12 months labels and data
        $labels = [];
        $incomeData = [];
        $expenseData = [];
        for($i = 0; $i < 12; $i++){
            $m = $startDate->copy()->addMonths($i);
            $key = $m->format('Y-m');
            $labels[] = $m->translatedFormat('M Y');
            $incomeData[] = (float) ($incomePerMonth[$key] ?? 0);
            $expenseData[] = (float) ($expensePerMonth[$key] ?? 0);
        }

        return view('admin.home',[
            "meta" => $this->meta(),
            "saldoKas" => $saldoKas,
            "incomeThisMonth" => $incomeThisMonth,
            "expenseThisMonth" => $expenseThisMonth,
            "unpaidCount" => $unpaidCount,
            "chartLabels" => json_encode($labels),
            "chartIncome" => json_encode($incomeData),
            "chartExpense" => json_encode($expenseData),
        ]);
    }
}
