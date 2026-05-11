<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\User;
use App\Models\House;
use App\Models\Resident;
use App\Models\FeeType;
use App\Models\ExpenseCategory;
use App\Models\PaymentBill;
use App\Models\Expense;
use Illuminate\Http\Request;

class APIController extends Controller
{
  public function user(User $data){
    return ApiFormatter::createApi(200,"Success",$data);
  }
  public function users(){
    return ApiFormatter::createApi(200,"Success",User::all());
  }
  public function house(House $data){
    return ApiFormatter::createApi(200,"Success",$data);
  }
  public function resident(Resident $data){
    return ApiFormatter::createApi(200,"Success",$data);
  }
  public function feeType(FeeType $data){
    return ApiFormatter::createApi(200,"Success",$data);
  }
  public function expenseCategory(ExpenseCategory $data){
    return ApiFormatter::createApi(200,"Success",$data);
  }
  public function paymentBill(PaymentBill $data){
    return ApiFormatter::createApi(200,"Success",$data);
  }
  public function expense(Expense $data){
    return ApiFormatter::createApi(200,"Success",$data);
  }
  
  public function residentsByHouse($houseId){
    $residents = Resident::where('house_id', $houseId)
                         ->where('is_active_resident', true)
                         ->orderBy('is_head_of_family', 'desc')
                         ->orderBy('full_name', 'asc')
                         ->get();
    return ApiFormatter::createApi(200,"Success",$residents);
  }
}
