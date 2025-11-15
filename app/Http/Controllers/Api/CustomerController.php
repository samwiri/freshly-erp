<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
class CustomerController extends Controller
{
public function index(Request $request){
    $userId = $request->user()?->id;
    $customers = Customer::with('user')
        ->when($userId, function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->get();
    return response()->json([
        'message' => 'Customers fetched successfully',
        'customers' => $customers,
    ], 200);
}

public function create(Request $request){
    $userId = $request->user()?->id;
    $validated = $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'required|email|unique:customers,email',
        'phone' => 'nullable|string|max:20',
        'dateOfBirth' => 'nullable|date',
        'gender' => 'required|string|in:male,female,other',
        'customerType' => 'required|string|in:individual,business',
        'taxId' => 'nullable|string|max:255',
        'company' => 'nullable|string|max:255',
        'preferredContactMethod' => 'nullable|string|in:email,phone,sms,whatsapp',
        'addresses' => 'nullable|array',
        'addresses.*.street' => 'nullable|string|max:255',
        'addresses.*.city' => 'nullable|string|max:255',
        'addresses.*.state' => 'nullable|string|max:255',
        'addresses.*.zip' => 'nullable|string|max:10',
        'addresses.*.isDefault' => 'nullable|boolean',
        'notes' => 'nullable|string',
        'tags' => 'nullable|array',
        'tags.*' => 'string|max:50',
    ]);

    $defaultAddress = collect($validated['addresses'] ?? [])->first(function ($addr) {
        return isset($addr['isDefault']) ? (bool)$addr['isDefault'] : true;
    }) ?? null;

    $payload = [
        'user_id' => $userId,
        'company_name' => $validated['company'] ?? null,
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
        'address' => $defaultAddress['street'] ?? null,
        'city' => $defaultAddress['city'] ?? null,
        'state' => $defaultAddress['state'] ?? null,
        'zip' => $defaultAddress['zip'] ?? null,
        'country' => null,
        'customer_type' => $validated['customerType'],
        'tax_id' => $validated['taxId'] ?? null,
        'tags' => $validated['tags'] ?? [],
        'notes' => $validated['notes'] ?? null,
        'preferences' => [
            'name' => $validated['name'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'dateOfBirth' => $validated['dateOfBirth'] ?? null,
            'preferredContactMethod' => $validated['preferredContactMethod'] ?? null,
            'addresses' => $validated['addresses'] ?? [],
        ],
    ];

    $customer = Customer::create($payload);
    $customer->customer_code = 'C' . str_pad((string)$customer->id, 5, '0', STR_PAD_LEFT);
    $customer->save();

    return response()->json([
        'message' => 'Customer created successfully',
        'customer' => $customer->fresh(),
    ], 201);
}
public function show($id){
    $customer = Customer::with('user')->find($id);
    if(!$customer){
        return response()->json([
            'message' => 'Customer not found',
        ], 404);
    }
    return response()->json([
        'message' => 'Customer fetched successfully',
        'customer' => $customer,
    ],
     200);
}

public function delete(Request $request, $id){
    $userId = $request->user()?->id;
   

    $customer = Customer::where('id', $id)->where('user_id', $userId)->first();
    if(!$customer){
        return response()->json([
            'message' => 'Customer not found or you are not authorized to delete this customer',
        ], 403);
    }

    $customer->delete();
    return response()->json([
        'message' => 'Customer deleted successfully',
    ], 200);
}
public function update(Request $request, $id){
    $customer = Customer::find($id);
    if(!$customer){
        return response()->json([
            'message' => 'Customer not found',
        ], 404);
    }
    $customer->update($request->all());
    return response()->json([
        'message' => 'Customer updated successfully',
        'customer' => $customer,
    ], 200);
}
}