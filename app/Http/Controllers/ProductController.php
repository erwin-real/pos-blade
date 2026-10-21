<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.products.index')->with('products', Product::with('category')->latest()->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.products.create')->with('categories', Category::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        //Handle File Upload
        if ($request->hasFile('cover_image')) {
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            $fileNameToStore = $filename .'_'.time().'.'.$extension;
            $path = $request->file('cover_image')->storeAs('products', $fileNameToStore, 'public');
        } else $fileNameToStore = 'noimage.jpg';

        $data['cover_image'] = $fileNameToStore;

        $product = Product::create($data);

        return redirect()->route('products.show', $product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('pages.products.show')->with('product', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('pages.products.edit')->with(['product' => $product, 'categories' => Category::all()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($product->cover_image) {
                // delete image
                $filePath = 'products/' . $product->cover_image;

                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
            
            // upload image
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            $fileNameToStore = $filename .'_'.time().'.'.$extension;
            $path = $request->file('cover_image')->storeAs('products', $fileNameToStore, 'public');
            $data['cover_image'] = $fileNameToStore;
        }

        $product->update($data);

        return redirect()->route('products.show', $product);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->cover_image) {
            // delete image
            $filePath = 'products/' . $product->cover_image;

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        $product->delete();
        return redirect()->route('products.index');
    }

    public function importProducts() {
        return view('pages.products.import');
    }

    public function importBatch(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.name' => 'required|string|max:255',
            'products.*.desc' => 'required|string|max:100',
            'products.*.srp' => 'required|numeric|min:0',
            'products.*.price' => 'required|numeric|min:0', 
            'products.*.stocks' => 'required|integer|min:0',
            'products.*.category' => 'required|string',
            'products.*.category_description' => 'nullable|string',
            'products.*.procurement' => 'nullable|integer',
            'products.*.expired_at' => 'nullable|date_format:m/d/Y' // 👈 Validate custom incoming date pattern
        ]);

        DB::beginTransaction();
        try {
            $syncCounter = 0;
            
            // Build an in-memory collection cache tracking the earliest date variations found inside the file itself
            $fileDateConsolidatorCache = [];
            foreach ($validated['products'] as $item) {
                $uniqueFileKey = trim($item['name']) . '|||' . trim($item['desc']);
                $incomingDateStr = !empty($item['expired_at']) ? trim($item['expired_at']) : null;

                if (!isset($fileDateConsolidatorCache[$uniqueFileKey])) {
                    $fileDateConsolidatorCache[$uniqueFileKey] = $incomingDateStr;
                } else {
                    // Compare duplicates found within the uploaded spreadsheet file itself
                    if ($incomingDateStr !== null) {
                        if ($fileDateConsolidatorCache[$uniqueFileKey] === null || Carbon::parse($incomingDateStr)->lt(Carbon::parse($fileDateConsolidatorCache[$uniqueFileKey]))) {
                            $fileDateConsolidatorCache[$uniqueFileKey] = $incomingDateStr;
                        }
                    }
                }
            }

            foreach ($validated['products'] as $item) {
                $productName = trim($item['name']);
                $productDesc = trim($item['desc']);
                $categoryName = trim($item['category']);
                $categoryDescFromFile = trim($item['category_description'] ?? '');

                // 1. Resolve Category ID reference hooks (Matches your existing criteria rules)
                $existingCategories = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->get();
                $matchedCategoryId = null;

                if ($existingCategories->isEmpty()) {
                    $newCategory = Category::create([
                        'name' => $categoryName,
                        'desc' => $categoryDescFromFile
                    ]);
                    $matchedCategoryId = $newCategory->id;
                } else {
                    foreach ($existingCategories as $cat) {
                        if (strtolower(trim($cat->desc ?? '')) === strtolower($categoryDescFromFile)) {
                            $matchedCategoryId = $cat->id;
                            break;
                        }
                    }
                    if (is_null($matchedCategoryId)) {
                        $forkedCategory = Category::create([
                            'name' => $categoryName,
                            'desc' => $categoryDescFromFile
                        ]);
                        $matchedCategoryId = $forkedCategory->id;
                    }
                }

                // 2. EARLIEST DATE EVALUATOR vs DATABASE RECORDS
                // Grab the consolidated date target calculated from the file cache loop above
                $uniqueFileKey = $productName . '|||' . $productDesc;
                $targetFileDate = $fileDateConsolidatorCache[$uniqueFileKey];

                // Check if this item already exists in your production table registry
                $existingProduct = Product::where('desc', $productDesc)->first();
                
                $finalExpirationDate = $targetFileDate;

                if ($existingProduct && $existingProduct->expired_at !== null) {
                    if ($finalExpirationDate !== null) {
                        $dbDate = Carbon::parse($existingProduct->expired_at);
                        $fileDate = Carbon::parse($finalExpirationDate);
                        
                        // Retain whichever date occurs earliest (FIFO)
                        $finalExpirationDate = $fileDate->lt($dbDate) ? $fileDate->format('Y-m-d') : $dbDate->format('Y-m-d');
                    } else {
                        // If file row holds no date, preserve what is already saved in the database
                        $finalExpirationDate = $existingProduct->expired_at;
                    }
                }

                // 3. Upsert execution updates matching the custom key field constraints
                Product::updateOrCreate(
                    ['desc' => $productDesc],
                    [
                        'name'        => $productName,
                        'price'       => $item['price'],       
                        'srp'         => $item['srp'],         
                        'stocks'      => $item['stocks'],
                        'category_id' => $matchedCategoryId,   
                        'procurement' => $item['procurement'] ?? 10,
                        'expired_at'  => Carbon::parse($finalExpirationDate)->format('Y-m-d')
                        // 'expired_at'  => $finalExpirationDate // 👈 Saves the earliest resolved date configuration
                    ]
                );
                $syncCounter++;
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => "Successfully evaluated and synced {$syncCounter} items. Earliest shelf-life limits calculated and applied."
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Database Processing Error: ' . $e->getMessage(), 'finalExpirationDate' => $finalExpirationDate, 'type finalExpirationDate' => gettype($finalExpirationDate) ], 422);
        }
    }
}
