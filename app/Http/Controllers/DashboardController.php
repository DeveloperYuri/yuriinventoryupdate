<?php

namespace App\Http\Controllers;

use App\Models\AssetitModel;
use App\Models\AtkModel;
use App\Models\ListAssetToolsModel;
use App\Models\ListSparePartModel;
use App\Models\SupplierModel;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $data['getSparepart'] = ListSparePartModel::count();
        $data['getAssettools'] = ListAssetToolsModel::count();
        $data['getSupplier'] = SupplierModel::count();
        $data['getAtk'] = AtkModel::count();
        $data['getAssetIT'] = AssetitModel::count();

        return view('dashboard.index', $data);
    }

    public function profile()
    {

        $users = Auth::user();

        return view('dashboard.users.userprofile', compact('users'));
    }
}
