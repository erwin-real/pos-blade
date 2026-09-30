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
        return view('pages.orders.index')->with('orders', Order::orderBy('created_at', 'desc')->get());
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

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $orderProfitAccumulator = 0;
            $itemsPayloadData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                if ($product->stocks < $item['quantity']) {
                    throw new Exception("Insufficient stock for item: {$product->name}");
                }

                // Calculate math values using backend database records
                $itemSubtotal = $product->srp * $item['quantity'];
                $itemCostBasis = $product->price * $item['quantity'];
                $itemProfitAmount = $itemSubtotal - $itemCostBasis;

                $subtotal += $itemSubtotal;
                $orderProfitAccumulator += $itemProfitAmount;

                // Stage processed data to execute cleanly inside sequential DB inserts
                $itemsPayloadData[] = [
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'unit_cost'   => $product->price,
                    'unit_price'  => $product->srp,
                    'subtotal'    => $itemSubtotal,
                    'item_profit' => $itemProfitAmount,
                    'product_model' => $product // reference pointer
                ];
            }

            // $taxAmount = $subtotal * 0.12;
            // $netAmount = $subtotal + $taxAmount;
            $netAmount = $subtotal;

            // 1. Save Main Transaction Header with computed total profit metrics
            $order = Order::create([
                'total_amount'    => $subtotal,
                'discount_amount' => 0.00,
                'net_amount'      => $netAmount,
                'amount_received' => $validated['amount_paid'],
                'change'          => $validated['change'],
                'total_profit'    => $orderProfitAccumulator, // 👈 Saved Profit Tracker
                'payment_method'  => $validated['payment_method'],
                'payment_status'  => 'completed',
            ]);

            // 2. Write Line Items and decrement inventories
            foreach ($itemsPayloadData as $data) {
                $order->order_items()->create([
                    'order_id'   => $order->id,
                    'product_id'  => $data['product_id'],
                    'quantity'    => $data['quantity'],
                    'unit_cost'   => $data['unit_cost'],
                    'unit_price'  => $data['unit_price'],
                    'subtotal'    => $data['subtotal'],
                    'item_profit' => $data['item_profit'],
                ]);

                $data['product_model']->decrement('stocks', $data['quantity']);
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Transaction compiled successfully.',
                'order_id' => $order->id
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return view('pages.orders.show')->with('order', $order);
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
