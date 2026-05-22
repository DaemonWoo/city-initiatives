<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInitiativeRequest;
use App\Http\Requests\UpdateInitiativeRequest;
use App\Models\Initiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class InitiativeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Initiatives/Index', [
            'initiatives' => Initiative::latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Initiatives/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInitiativeRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('initiatives', 'public');
        }

        $request->user()->initiatives()->create($data);

        return redirect()->route('initiatives.index')->with('success', 'Initiative created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Initiative $initiative): Response
    {
        return Inertia::render('Initiatives/Show', [
            'initiative' => $initiative->load('user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Initiative $initiative): Response
    {
        return Inertia::render('Initiatives/Edit', [
            'initiative' => $initiative,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInitiativeRequest $request, Initiative $initiative): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            if ($initiative->image) {
                Storage::disk('public')->delete($initiative->image);
            }

            $data['image'] = $request->file('image')->store('initiatives', 'public');
        }

        $initiative->update($data);

        return redirect()->route('initiatives.index')->with('success', 'Initiative updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Initiative $initiative): RedirectResponse
    {
        $initiative->delete();

        return redirect()
            ->route('initiatives.index')
            ->with('success', 'Initiative deleted.');
    }
}
