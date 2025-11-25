<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\ServiceItem;
class ServiceItemController extends Controller
{
    public function index(Request $request){
        $userId = $request->user()->id;
        $serviceItem = ServiceItem::where('user_id', $userId)->get();
        return response()->json($serviceItem);
    }
    public function create(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required',
            'category' => 'required',
            'description' => 'required',
            'base_price' => 'required',
            'unit' => 'required',
            'duration' => 'required',
            'is_active' => 'required',
            'tags' => 'required',
        ]);
        $userId = $request->user()->id;
        $serviceItem = ServiceItem::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'base_price' => $validated['base_price'],
            'unit' => $validated['unit'],
            'duration' => $validated['duration'],
            'tags' => $validated['tags'],
            'user_id' => $userId,
        ]);
        return response()->json($serviceItem);
    }
    public function show($id){
        $serviceItem =  ServiceItem::find($id);
        return response()->json($serviceItem);
    }
    public function update(Request $request, $id){
        $serviceItem = ServiceItem::find($id);
        $serviceItem->update([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'base_price' => $request->base_price,
            'unit' => $request->unit,
            'duration' => $request->duration,
            'is_active' => $request->is_active,
            'tags' => $request->tags,
        ]);
        return response()->json($serviceItem);
    }
    public function destroy($id){
        $serviceItem = ServiceItem::find($id);
        $serviceItem->delete();
        return response()->json($serviceItem);
    }
}
