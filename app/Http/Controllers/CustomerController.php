<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Account;
use App\Models\MetalType;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('customer.index', [
            'customers' => Customer::with('account')->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'customer_type' => 'required|string|in:Retail,Institutional',
        ]);

        $customer = Customer::create($validated);

        $customer->account()->create([
            'account_number' => $this->generateAccountNumber(),
        ]);

        // Feedback if successful or not
        if ($customer) {
            return redirect()->route('customers')->with('success', 'Customer created successfully');
        } else {
            return redirect()->route('customers.create')->with('error', 'Failed to create customer');
        }
    }

    // Function to generate the account number
    private function generateAccountNumber(): string
    {
        do {
            $accountNumber = 'ACC-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Account::where('account_number', $accountNumber)->exists());
        return $accountNumber;
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load(['account.holding.metalType', 'account.deposit.metalType']);

        $metalTypes = MetalType::query()->orderBy('name')->get(['id', 'name']);

        return view('customer.show', [
            'customer' => $customer,
            'metalTypes' => $metalTypes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
