<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['getRecord'] = SupplierModel::getRecord($request);
        return view('dashboard.supplier.listsupplier', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.supplier.createsupplier');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $supplier = request()->validate([
            'name' =>'required',
            'email' =>'required',
            'address' =>'required',
            'contact' =>'required'
        ]);

        $supplier = new SupplierModel();

        $supplier->name = trim($request->name);
        $supplier->email = trim($request->email);
        $supplier->address = trim($request->address);
        $supplier->contact = trim($request->contact);
        $supplier->description = trim($request->description);
        $supplier->save();

        return redirect('/supplier')->with('success', 'Create New Supplier Successfully');
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
        $supplier = SupplierModel::findOrFail($id);
        
        return view('dashboard.supplier.editsupplier', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brand = SupplierModel::findorFail($id);

        $brand->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'contact' => $request->contact,
            'description' => $request->description
        ]);

        return redirect('/supplier')->with('success', 'Update Supplier Sucessfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = SupplierModel::findorFail($id);
        $brand->delete();

        return redirect('/supplier')->with('success', 'Delete Supplier Successfully');
    }
}
