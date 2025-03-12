<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    public function statistique(){

            if(!auth()->user()->can('view_dashboard')){
                return [ 'message' => "vous n’avez pas la permission de voir le tableau de bord."];
            }
    
            $AdminDashboardData = [
                'totalUsers' => 20,
                'totalProducts' => 30,
                'totalCategories' => 10,
                'totalSubCategories' => 60,
            ];
    
            return [
                'statistique' => $AdminDashboardData
            ];
        }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
