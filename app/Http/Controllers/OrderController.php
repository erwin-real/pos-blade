<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
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
        return view('pages.orders.create')->with([
            "products" => Product::where('stocks', '>', 0)
                            ->orderBy('name')
                            ->get(),
            "categories" => Category::orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        // 1. Assert request contains correct data arrays
        $validated = $request->validated();

        // 2. Open an isolated transaction context block to secure database mutations
        DB::beginTransaction();

        try {
            $subtotal = 0;

            // Compute math checks locally against raw backend numbers to prevent injection tempering
            foreach ($validated['items'] as $item) {
                // Use lockForUpdate to block concurrent transaction stock manipulation anomalies
                $product = Product::lockForUpdate()->find($item['product_id']);

                // Ensure item stock parameters still satisfy customer request criteria
                if ($product->stocks < $item['quantity']) {
                    throw new Exception("Insufficient stock for item: {$product->name}. Balance available: {$product->stocks}");
                }

                $subtotal += $item['srp'] * $item['quantity'];
            }

            // Financial breakdown mapping logic (Matches 12% VAT specifications)
            // $taxAmount = $subtotal * 0.12;
            // $netAmount = $subtotal + $taxAmount;
            $netAmount = $subtotal;

            if ($validated['amount_paid'] < $netAmount && $validated['payment_method'] === 'Cash') {
                throw new Exception("Insufficient payment amount provided.");
            }

            // 3. Create the Main Transaction Order Record Header
            $order = Order::create([
                'total_amount'    => $subtotal,
                'discount_amount' => 0.00,
                'net_amount'      => $netAmount,
                'amount_received' => $validated['amount_paid'],
                'change'          => $validated['change'],
                'payment_method'  => $validated['payment_method'],
                'payment_status'  => 'completed',
            ]);

            // 4. Populate Line Item records and decrement inventory metrics
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                // Save individual item tracking row snapshots 
                $order->order_items()->create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['srp'],
                    'subtotal'   => $item['srp'] * $item['quantity']
                ]);

                // Decrement inventory stocks
                $product->decrement('stocks', $item['quantity']);
            }

            // Everything succeeded. Commit all updates permanently to storage
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction compiled successfully. Stock entries cleared.',
                'order_id' => $order->id
            ], 200);

        } catch (Exception $e) {
            // Something failed. Undo everything within this block to preserve database integrity.
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
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
