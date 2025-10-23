<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
class CustomerController extends Controller
{
public function index(){
    $customers = Customer::all();
    return response()->json([
        'message' => 'Customers fetched successfully',
        'customers' => $customers,
    ], 200);
}
public function show($id){
    $customer = Customer::find($id);
    return response()->json([
        'message' => 'Customer fetched successfully',
        'customer' => $customer,
    ], 200);
}
public function update(Request $request, $id){
}
}