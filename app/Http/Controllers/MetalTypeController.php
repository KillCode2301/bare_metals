<?php

namespace App\Http\Controllers;

use App\Models\MetalType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MetalTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('metal-type.index', [
            'metalTypes' => MetalType::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('metal-types.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:metal_types,code',
            'name' => 'required|string|max:255|unique:metal_types,name',
            'current_price_per_kg' => 'required|numeric|min:0',
        ]);

        $metalType = MetalType::create($validated);

        if ($metalType) {
            return redirect()->route('metal-types.index')->with('success', 'Metal type created successfully');
        }

        return redirect()->route('metal-types.index')->with('error', 'Failed to create metal type');
    }

    /**
     * Display the specified resource.
     */
    public function show(MetalType $metalType)
    {
        return redirect()->route('metal-types.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MetalType $metalType)
    {
        return view('metal-type.edit', [
            'metalType' => $metalType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetalType $metalType)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('metal_types', 'code')->ignore($metalType->id),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('metal_types', 'name')->ignore($metalType->id),
            ],
            'current_price_per_kg' => 'required|numeric|min:0',
        ]);

        $metalType->update($validated);

        return redirect()->route('metal-types.index')->with('success', 'Metal type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetalType $metalType)
    {
        // Soft business rule before delete: block if any custody record still references this metal.
        $inUse = $metalType->deposit()->exists() || $metalType->withdrawal()->exists() || $metalType->holding()->exists() || $metalType->allocatedBar()->exists();

        if ($inUse) {
            return redirect()->route('metal-types.index')->with('error', 'This metal type cannot be deleted because it is referenced by deposits, holdings, or other records.');
        }

        // Fallback if FK or races bypass the exists() checks: show friendly message instead of SQL error.
        try {
            $metalType->delete();
        } catch (QueryException) {
            return redirect()->route('metal-types.index')->with('error', 'This metal type cannot be deleted because it is still in use.');
        }

        return redirect()->route('metal-types.index')->with('success', 'Metal type deleted successfully.');
    }
}
