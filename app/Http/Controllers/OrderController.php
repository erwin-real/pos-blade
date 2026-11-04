<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = null;
        $selectedUserId = null;
        $users = null;
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $ordersQuery = Order::whereDate('created_at', $selectedDate);

        if (auth()->user()->type != 'admin') {
            $orders = $ordersQuery->where('user_id', Auth::id())->with('order_items.product')->get();
            $totalSalesSum = $ordersQuery->where('user_id', Auth::id())->sum('total_amount');
        } else {
            $users = User::orderBy('name', 'asc')->get();
            $selectedUserId = $request->input('user_id', 'all');
            
            $ordersQuery->when($selectedUserId !== 'all', function ($query) use ($selectedUserId) {
                return $query->where('user_id', $selectedUserId);
            });

            $orders = $ordersQuery->with(['user', 'order_items.product'])->get();
            $totalSalesSum = $ordersQuery->sum('total_amount');
        }

        $totalCashSales = $orders->where('payment_method', 'Cash')->sum('total_amount');
        $totalGCashSales = $orders->where('payment_method', 'GCash')->sum('total_amount');

        return view('pages.orders.index', compact(
            'selectedDate', 'users', 'selectedUserId', 'orders', 'totalSalesSum', 'totalCashSales', 'totalGCashSales'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.orders.create')->with([
            "products" => Product::orderBy('name')->get(),
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
                // Check if this is a custom on-the-fly product
                if (empty($item['product_id']) || $item['product_id'] == 0) {
                    // 1. Create the new product dynamically
                    $product = Product::create([
                        'name' => $item['name'], // Ensure 'name' is captured from your custom form
                        'desc' => $item['desc'],          // Created with 0 initial inventory
                        'stocks' => $item['quantity'],          // Created with 0 initial inventory
                        'price' => $item['srp'] * 0.8, // If your POS doesn't ask cashiers for dynamic wholesale cost basis, fall back to retail price (0 profit margin)
                        'srp' => $item['srp'], 
                        'procurement' => 1
                        // Add other required default fields for your system here (e.g., code, category_id)
                    ]);
                    
                    // Set quantity to 0 on database level so decrementing below won't trigger negative stock checks if your models guard it, 
                    // OR leave it at 0 to explicitly show a temporary -qty stock deviation until back-office reconciles it.
                } else {
                    // Fetch existing standard inventory item
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    if (!$product) {
                        throw new Exception("Product ID {$item['product_id']} not found.");
                    }

                    // if ($product->stocks < $item['quantity']) {
                    //     throw new Exception("Insufficient stock for item: {$product->name}");
                    // }
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
                'user_id'         => $validated['user_id'],
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
        if (auth()->user()->type != 'admin' && Auth::id() != $order->user_id) {
            return redirect()->route('orders.index');
        }

        return view('pages.orders.show')->with('order', $order);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        if (auth()->user()->type != 'admin' && Auth::id() != $order->user_id) {
            return redirect()->route('orders.index');
        }

        return view('pages.orders.edit')->with('order', $order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->validated();

        $originalTime = Carbon::parse($order->created_at)->toTimeString();

        $order->created_at = Carbon::parse($data['created_at'] . ' ' . $originalTime);

        $order->payment_method = $data['payment_method'];

        $order->save(['timestamps' => false]);

        return redirect()->route('orders.show', $order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        foreach ($order->order_items as $item) {
            $product = Product::lockForUpdate()->find($item->product_id);

            if ($product) {
                $product->increment('stocks', $item->quantity);
            }

            $item->delete();
        }

        $order->delete();
        return redirect()->route('orders.index');
    }
}
