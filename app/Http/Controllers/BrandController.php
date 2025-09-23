<?php

namespace App\Http\Controllers;

use App\Models\BrandModel;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['getRecord'] = BrandModel::getRecord($request);

        return view('dashboard.brand.listbrand', $data);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.brand.createbrand');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = request()->validate([
            'name' =>'required',
        ]);

        $user = new BrandModel();

        $user->name = trim($request->name);
        $user->save();

        return redirect('/brand')->with('success', 'Create New Brand Successfully');
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
        $brand = BrandModel::findOrFail($id);
        
        return view('dashboard.brand.editbrand', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brand = BrandModel::findorFail($id);

        $brand->update([
            'name' => $request->name
        ]);

        return redirect('/brand')->with('success', 'Update Brand Sucessfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = BrandModel::findorFail($id);
        $brand->delete();

        return redirect('/brand')->with('success', 'Delete Brand Successfully');
    }
}
