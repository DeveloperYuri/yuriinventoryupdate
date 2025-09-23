<?php

namespace App\Http\Controllers;

use App\Models\WarehouseModel;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['getRecord'] = WarehouseModel::getRecord($request);
        return view('dashboard.warehouse.listwarehouse', $data);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.warehouse.createwarehouse');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = request()->validate([
            'name' =>'required',
            'address' =>'required',
            'pic' =>'required',
        ]);

        $user = new WarehouseModel();

        $user->name = trim($request->name);
        $user->address = trim($request->address);
        $user->pic = trim($request->pic);
        $user->save();

        return redirect('/warehouse')->with('success', 'Create New Warehouse Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $warehouse = WarehouseModel::findOrFail($id);
        
        return view('dashboard.warehouse.editwarehouse', compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $warehouse = WarehouseModel::findorFail($id);

        $warehouse->update([
            'name' => $request->name,
            'address' => $request->address,
            'pic' => $request->pic
        ]);

        return redirect('/warehouse')->with('success', 'Update Warehouse Sucessfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warehouse = WarehouseModel::findorFail($id);
        $warehouse->delete();

        return redirect('/warehouse')->with('success', 'Delete Warehouse Successfully');
    }
}
