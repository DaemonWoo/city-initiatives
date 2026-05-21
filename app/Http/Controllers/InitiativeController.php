<?php

namespace App\Http\Controllers;

use App\Models\Initiative;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InitiativeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Initiatives/Index', [
            'initiatives' => Initiative::latest()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Initiatives/Create');
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
    public function show(Initiative $initiative)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Initiative $initiative)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Initiative $initiative)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Initiative $initiative)
    {
        //
    }
}
