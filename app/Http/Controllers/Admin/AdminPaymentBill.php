<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Meta;
use App\Models\House;
use App\Models\FeeType;
use App\Models\PaymentBill;
use App\Models\Payment;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminPaymentBill extends Controller
{

    private function meta(){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Admin | Tagihan Bulanan';
        return $meta;
    }

    public function index(Request $request){
        $month = $request->get('month', date('Y-m'));
        $billingMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

        $bills = PaymentBill::with(['house', 'resident', 'feeType', 'payment'])
            ->whereYear('billing_month', $billingMonth->year)
            ->whereMonth('billing_month', $billingMonth->month)
            ->orderBy('house_id', 'ASC')
            ->orderBy('fee_type_id', 'ASC')
            ->get();

        return view('admin.payment-bill',[
            "meta" => $this->meta(),
            "bills" => $bills,
            "month" => $month,
            "houses" => House::with('activeResidents')->orderBy('house_number','ASC')->get(),
            "feeTypes" => FeeType::where('is_active', true)->orderBy('name','ASC')->get(),
        ]);
    }

    public function postHandler(Request $request){
        if($request->submit=="store"){
            $res = $this->store($request);
            return back()->with($res['status'],$res['message']);
        }
        if($request->submit=="destroy"){
            $res = $this->destroy($request);
            return back()->with($res['status'],$res['message']);
        }
    }

    public function store(Request $request){
        $request->validate([
            'house_id' => 'required|numeric',
            'resident_id' => 'required|numeric',
            'fee_type_id' => 'required|numeric',
            'billing_month' => 'required|date_format:Y-m',
        ]);

        $house = House::find($request->house_id);
        $resident = Resident::find($request->resident_id);
        $feeType = FeeType::find($request->fee_type_id);

        if(!$house || !$feeType || !$resident){
            return ['status' => 'error', 'message' => 'Data rumah, penghuni, atau jenis iuran tidak ditemukan'];
        }

        // Validasi resident harus dari rumah yang dipilih
        if($resident->house_id != $house->id){
            return ['status' => 'error', 'message' => 'Penghuni tidak sesuai dengan rumah yang dipilih'];
        }

        $billingMonth = Carbon::createFromFormat('Y-m', $request->billing_month)->startOfMonth();

        // Cek apakah tagihan sudah ada
        $exists = PaymentBill::where('house_id', $house->id)
            ->where('fee_type_id', $feeType->id)
            ->where('billing_month', $billingMonth->format('Y-m-d'))
            ->exists();

        if($exists){
            return ['status' => 'error', 'message' => 'Tagihan untuk bulan ini sudah ada'];
        }

        PaymentBill::create([
            'house_id' => $house->id,
            'resident_id' => $resident->id,
            'fee_type_id' => $feeType->id,
            'billing_month' => $billingMonth->format('Y-m-d'),
            'amount' => $feeType->amount,
            'status' => 'unpaid',
        ]);

        return ['status' => 'success', 'message' => 'Tagihan berhasil ditambahkan'];
    }

    /**
     * Generate tagihan bulanan untuk semua rumah yang punya penghuni aktif
     */
    public function generate(Request $request){
        $request->validate([
            'billing_month' => 'required|date_format:Y-m',
        ]);

        $billingMonth = Carbon::createFromFormat('Y-m', $request->billing_month)->startOfMonth();
        $feeTypes = FeeType::where('is_active', true)->get();
        $houses = House::with(['headOfFamily', 'activeResidents'])->whereHas('activeResidents')->get();

        $generated = 0;

        foreach($houses as $house){
            // Prioritas: kepala keluarga, jika tidak ada ambil resident pertama
            $resident = $house->headOfFamily ?? $house->activeResidents->first();
            
            foreach($feeTypes as $feeType){
                // Skip jika tagihan sudah ada
                $exists = PaymentBill::where('house_id', $house->id)
                    ->where('fee_type_id', $feeType->id)
                    ->where('billing_month', $billingMonth->format('Y-m-d'))
                    ->exists();

                if(!$exists){
                    PaymentBill::create([
                        'house_id' => $house->id,
                        'resident_id' => $resident ? $resident->id : null,
                        'fee_type_id' => $feeType->id,
                        'billing_month' => $billingMonth->format('Y-m-d'),
                        'amount' => $feeType->amount,
                        'status' => 'unpaid',
                    ]);
                    $generated++;
                }
            }
        }

        return back()->with('success', "Berhasil generate {$generated} tagihan untuk bulan {$billingMonth->format('F Y')}");
    }

    /**
     * Bayar satu tagihan
     */
    public function pay(Request $request){
        $request->validate([
            'bill_id' => 'required|numeric',
            'payment_method' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $bill = PaymentBill::find($request->bill_id);

        if(!$bill){
            return back()->with('error', 'Tagihan tidak ditemukan');
        }

        if($bill->status == 'paid'){
            return back()->with('error', 'Tagihan sudah lunas');
        }

        Payment::create([
            'bill_id' => $bill->id,
            'paid_at' => now(),
            'payment_method' => $request->payment_method,
            'note' => $request->note,
        ]);

        $bill->update(['status' => 'paid']);

        return back()->with('success', 'Pembayaran berhasil dicatat');
    }

    /**
     * Bayar tagihan bulk (beberapa bulan sekaligus)
     */
    public function payBulk(Request $request){
        $request->validate([
            'house_id' => 'required|numeric',
            'resident_id' => 'required|numeric',
            'fee_type_id' => 'required|numeric',
            'start_month' => 'required|date_format:Y-m',
            'months' => 'required|numeric|min:1|max:12',
            'payment_method' => 'nullable|string',
        ]);

        $house = House::find($request->house_id);
        $resident = Resident::find($request->resident_id);
        $feeType = FeeType::find($request->fee_type_id);

        if(!$house || !$feeType || !$resident){
            return back()->with('error', 'Data rumah, penghuni, atau jenis iuran tidak ditemukan');
        }

        // Validasi resident harus dari rumah yang dipilih
        if($resident->house_id != $house->id){
            return back()->with('error', 'Penghuni tidak sesuai dengan rumah yang dipilih');
        }

        $startMonth = Carbon::createFromFormat('Y-m', $request->start_month)->startOfMonth();
        $paid = 0;
        $failed = 0;

        for($i = 0; $i < $request->months; $i++){
            $currentMonth = $startMonth->copy()->addMonths($i);
            
            // Cek apakah tagihan sudah ada
            $existingBill = PaymentBill::where('house_id', $house->id)
                ->where('fee_type_id', $feeType->id)
                ->where('billing_month', $currentMonth->format('Y-m-d'))
                ->first();

            if($existingBill){
                // Jika tagihan sudah ada dan sudah dibayar, skip
                if($existingBill->status == 'paid'){
                    $failed++;
                    continue;
                }
                // Jika tagihan ada tapi belum dibayar, bayar saja
                $bill = $existingBill;
            } else {
                // Buat tagihan baru
                $bill = PaymentBill::create([
                    'house_id' => $house->id,
                    'resident_id' => $resident->id,
                    'fee_type_id' => $feeType->id,
                    'billing_month' => $currentMonth->format('Y-m-d'),
                    'amount' => $feeType->amount,
                    'status' => 'unpaid',
                ]);
            }

            // Bayar tagihan
            Payment::create([
                'bill_id' => $bill->id,
                'paid_at' => now(),
                'payment_method' => $request->payment_method,
                'note' => "Pembayaran bulk {$request->months} bulan",
            ]);

            $bill->update(['status' => 'paid']);
            $paid++;
        }

        $message = "Berhasil membayar {$paid} tagihan {$feeType->name} untuk rumah {$house->house_number}";
        if($failed > 0){
            $message .= ". {$failed} tagihan sudah dibayar sebelumnya.";
        }
        return back()->with('success', $message);
    }

    public function destroy(Request $request){
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $bill = PaymentBill::find($request->id);

        if(!$bill){
            return ['status' => 'error', 'message' => 'Tagihan tidak ditemukan'];
        }

        if($bill->status == 'paid'){
            return ['status' => 'error', 'message' => 'Tidak dapat menghapus tagihan yang sudah dibayar'];
        }

        $bill->delete();
        return ['status' => 'success', 'message' => 'Tagihan berhasil dihapus'];
    }
}
