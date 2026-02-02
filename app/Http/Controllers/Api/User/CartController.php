<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartItemOption;
use App\Models\CartItemAddon;
use App\Models\Product;
use App\Services\LocalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Get user's cart with all items
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();

        $cart = Cart::with([
            // 'store:id,name_en,name_ar,description_en,description_ar,logo,status',
            'items.product:id,name_en,name_ar,description_en,description_ar,base_price,offer_price,is_active',
            'items.product.primaryImage',
            'items.options.productOptionValue.productOption.optionGroup:id,name_en,name_ar',
            'items.options.productOptionValue.optionValue:id,value_en,value_ar',
            'items.options.productOptionValue:id,product_option_id,option_value_id,price_type,price_value',
            'items.addons.addon:id,name_en,name_ar,description_en,description_ar,price,is_active'
        ])->where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Cart']),
                'data' => []
            ], 200);
        }

        // Transform cart data
        $cartData = $this->transformCart($cart);

        // Localize the data
        $localizedCart = LocalizationService::localizeCollection([$cartData], ['name', 'description', 'value'])[0];

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'cart' => $localizedCart,
            ],
        ], 200);
    }

    /**
     * Add item to cart
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItem(Request $request)
    {

        Log::info($request->all());
        $user = auth('api')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
            'option_values' => 'nullable|array',
            'option_values.*' => 'exists:product_option_values,id',
            'addons' => 'nullable|array',
            'addons.*.addon_id' => 'required|exists:addons,id',
            'addons.*.quantity' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get product with store
        $product = Product::find($request->product_id);

        if (!$product || !$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Product']),
            ], 404);
        }

        // if (!$product->store || $product->store->status !== 'approved') {
        //     return response()->json([
        //         'success' => false,
        //         'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Product']),
        //     ], 404);
        // }

        // Check if user has a cart
        $cart = Cart::where('user_id', $user->id)->first();

        // Check for store conflict
        //  if ($cart && $cart->store_id !== $product->store_id) {
        //     $cart->delete();
        //     $cart = null;
        // }


        DB::beginTransaction();
        try {
            // Create cart if doesn't exist
            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $user->id,
                    // 'store_id' => $product->store_id,
                ]);
            }

            // Check for existing cart item with same product, options, and addons
            $existingItem = $this->findExistingCartItem($cart, $product->id, $request->option_values ?? [], $request->addons ?? []);
            
            if ($existingItem) {
                // Update quantity of existing item
                $newQuantity = $existingItem->quantity + $request->quantity;
                
                // Check if new quantity exceeds maximum
                if ($newQuantity > 99) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => LocalizationService::getMessage('cart.quantity_limit_exceeded'),
                        'errors' => [
                            'quantity' => [LocalizationService::getMessage('cart.max_quantity_per_item')],
                        ],
                    ], 422);
                }
                
                $existingItem->update(['quantity' => $newQuantity]);
                $cartItem = $existingItem;
            } else {
                // Check cart item limit for new items only
                if ($cart->items()->count() >= 50) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => LocalizationService::getMessage('cart.limit_reached'),
                        'errors' => [
                            'cart' => [LocalizationService::getMessage('cart.max_items')],
                        ],
                    ], 422);
                }

                // Create new cart item
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                ]);

                // Add options
                if ($request->has('option_values') && is_array($request->option_values)) {
                    foreach ($request->option_values as $optionValueId) {
                        CartItemOption::create([
                            'cart_item_id' => $cartItem->id,
                            'product_option_value_id' => $optionValueId,
                        ]);
                    }
                }

                // Add addons
                if ($request->has('addons') && is_array($request->addons)) {
                    foreach ($request->addons as $addon) {
                        CartItemAddon::create([
                            'cart_item_id' => $cartItem->id,
                            'addon_id' => $addon['addon_id'],
                            'quantity' => $addon['quantity'] ?? 1,
                        ]);
                    }
                }
            }

            DB::commit();

            // Reload cart with relationships
            $cart->load([
                // 'store:id,name_en,name_ar,description_en,description_ar,logo,status',
                'items.product:id,name_en,name_ar,description_en,description_ar,base_price,is_active',
                'items.product.primaryImage',
                'items.options.productOptionValue.productOption.optionGroup:id,name_en,name_ar',
                'items.options.productOptionValue.optionValue:id,value_en,value_ar',
                'items.options.productOptionValue:id,product_option_id,option_value_id,price_type,price_value',
                'items.addons.addon:id,name_en,name_ar,description_en,description_ar,price,is_active'
            ]);

            // Transform and localize
            $cartData = $this->transformCart($cart);
            $localizedCart = LocalizationService::localizeCollection([$cartData], ['name', 'description', 'value'])[0];

            $message = $existingItem ? 
                LocalizationService::getMessage('cart.quantity_updated') : 
                LocalizationService::getMessage('cart.item_added');
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'cart' => $localizedCart,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.server_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     *
     * @param Request $request
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQuantity(Request $request, $itemId)
    {
        $user = auth('api')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find cart item
        $cartItem = CartItem::whereHas('cart', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->find($itemId);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Cart item']),
            ], 404);
        }

        // Update quantity
        $cartItem->update(['quantity' => $request->quantity]);

        // Reload cart
        $cart = $cartItem->cart;
       $cart->load([
            // 'store:id,name_en,name_ar,description_en,description_ar,logo,status',
            'items.product:id,name_en,name_ar,description_en,description_ar,base_price,is_active',
            'items.product.primaryImage',
            'items.options.productOptionValue.productOption.optionGroup:id,name_en,name_ar',
            'items.options.productOptionValue.optionValue:id,value_en,value_ar',
            'items.options.productOptionValue:id,product_option_id,option_value_id,price_type,price_value',
            'items.addons.addon:id,name_en,name_ar,description_en,description_ar,price,is_active'
        ]);

        // Transform and localize
        $cartData = $this->transformCart($cart);
        $localizedCart = LocalizationService::localizeCollection([$cartData], ['name', 'description', 'value'])[0];

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('cart.quantity_updated'),
            'data' => [
                'cart' => $localizedCart,
            ],
        ], 200);
    }

    /**
     * Update cart item (options, addons, quantity)
     *
     * @param Request $request
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateItem(Request $request, $itemId)
    {
        $user = auth('api')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'quantity' => 'nullable|integer|min:1|max:99',
            'option_values' => 'nullable|array',
            'option_values.*' => 'exists:product_option_values,id',
            'addons' => 'nullable|array',
            'addons.*.addon_id' => 'required|exists:addons,id',
            'addons.*.quantity' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find cart item
        $cartItem = CartItem::with(['options', 'addons', 'product'])
            ->whereHas('cart', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->find($itemId);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Cart item']),
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Update quantity if provided
            if ($request->has('quantity')) {
                $cartItem->update(['quantity' => $request->quantity]);
            }

            // Update options if provided
            if ($request->has('option_values')) {
                // Delete existing options
                $cartItem->options()->forceDelete();

                // Add new options
                foreach ($request->option_values as $optionValueId) {
                    CartItemOption::create([
                        'cart_item_id' => $cartItem->id,
                        'product_option_value_id' => $optionValueId,
                    ]);
                }
            }

            // Update addons if provided
            if ($request->has('addons')) {
                // Delete existing addons
                $cartItem->addons()->forceDelete();

                // Add new addons
                foreach ($request->addons as $addon) {
                    CartItemAddon::create([
                        'cart_item_id' => $cartItem->id,
                        'addon_id' => $addon['addon_id'],
                        'quantity' => $addon['quantity'] ?? 1,
                    ]);
                }
            }

            DB::commit();

            // Reload cart
            $cart = $cartItem->cart;
            $cart->load([
                // 'store:id,name_en,name_ar,description_en,description_ar,logo,status',
                'items.product:id,name_en,name_ar,description_en,description_ar,base_price,is_active',
                'items.product.primaryImage',
                'items.options.productOptionValue.productOption.optionGroup:id,name_en,name_ar',
                'items.options.productOptionValue.optionValue:id,value_en,value_ar',
                'items.options.productOptionValue:id,product_option_id,option_value_id,price_type,price_value',
                'items.addons.addon:id,name_en,name_ar,description_en,description_ar,price,is_active'
            ]);

            // Transform and localize
            $cartData = $this->transformCart($cart);
            $localizedCart = LocalizationService::localizeCollection([$cartData], ['name', 'description', 'value'])[0];

            return response()->json([
                'success' => true,
                'message' => LocalizationService::getMessage('cart.item_updated'),
                'data' => [
                    'cart' => $localizedCart,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.server_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove item from cart
     *
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItem($itemId)
    {
        $user = auth('api')->user();

        // Find cart item
        $cartItem = CartItem::whereHas('cart', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->find($itemId);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Cart item']),
            ], 404);
        }

        $cart = $cartItem->cart;
        $isLastItem = $cart->items()->count() === 1;

        // Delete item
        $cartItem->delete();

        // If last item, delete cart
        if ($isLastItem) {
            $cart->delete();
            return response()->json([
                'success' => true,
                'message' => LocalizationService::getMessage('cart.cleared'),
            ], 204);
        }

        // Reload cart
        $cart->load([
            // 'store:id,name_en,name_ar,description_en,description_ar,logo,status',
            'items.product:id,name_en,name_ar,description_en,description_ar,base_price,is_active',
            'items.product.primaryImage',
            'items.options.productOptionValue.productOption.optionGroup:id,name_en,name_ar',
            'items.options.productOptionValue.optionValue:id,value_en,value_ar',
            'items.options.productOptionValue:id,product_option_id,option_value_id,price_type,price_value',
            'items.addons.addon:id,name_en,name_ar,description_en,description_ar,price,is_active'
        ]);

        // Transform and localize
        $cartData = $this->transformCart($cart);
        $localizedCart = LocalizationService::localizeCollection([$cartData], ['name', 'description', 'value'])[0];

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('cart.item_removed'),
            'data' => [
                'cart' => $localizedCart,
            ],
        ], 200);
    }

    /**
     * Clear entire cart
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        $user = auth('api')->user();

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Cart']),
            ], 404);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('cart.cleared'),
        ], 200);
    }

    /**
     * Replace cart with product from different store
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function replace(Request $request)
    {
        $user = auth('api')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
            'option_values' => 'nullable|array',
            'option_values.*' => 'exists:product_option_values,id',
            'addons' => 'nullable|array',
            'addons.*.addon_id' => 'required|exists:addons,id',
            'addons.*.quantity' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get product
        $product = Product::find($request->product_id);

        if (!$product || !$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Product']),
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Delete old cart
            Cart::where('user_id', $user->id)->delete();

            // Create new cart
            $cart = Cart::create([
                'user_id' => $user->id,
                // 'store_id' => $product->store_id,
            ]);

            // Create cart item
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);

            // Add options
            if ($request->has('option_values') && is_array($request->option_values)) {
                foreach ($request->option_values as $optionValueId) {
                    CartItemOption::create([
                        'cart_item_id' => $cartItem->id,
                        'product_option_value_id' => $optionValueId,
                    ]);
                }
            }

            // Add addons
            if ($request->has('addons') && is_array($request->addons)) {
                foreach ($request->addons as $addon) {
                    CartItemAddon::create([
                        'cart_item_id' => $cartItem->id,
                        'addon_id' => $addon['addon_id'],
                        'quantity' => $addon['quantity'] ?? 1,
                    ]);
                }
            }

            DB::commit();

            // Reload cart
            $cart->load([
                // 'store:id,name_en,name_ar,description_en,description_ar,logo,status',
                'items.product:id,name_en,name_ar,description_en,description_ar,base_price,is_active',
                'items.product.primaryImage',
                'items.options.productOptionValue.productOption.optionGroup:id,name_en,name_ar',
                'items.options.productOptionValue.optionValue:id,value_en,value_ar',
                'items.options.productOptionValue:id,product_option_id,option_value_id,price_type,price_value',
                'items.addons.addon:id,name_en,name_ar,description_en,description_ar,price,is_active'
            ]);
            // Transform and localize
            $cartData = $this->transformCart($cart);
            $localizedCart = LocalizationService::localizeCollection([$cartData], ['name', 'description', 'value'])[0];

            return response()->json([
                'success' => true,
                'message' => LocalizationService::getMessage('cart.replaced'),
                'data' => [
                    'cart' => $localizedCart,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.server_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Find existing cart item with same product, options, and addons
     *
     * @param Cart $cart
     * @param int $productId
     * @param array $optionValues
     * @param array $addons
     * @return CartItem|null
     */
    private function findExistingCartItem(Cart $cart, $productId, array $optionValues = [], array $addons = [])
    {
        $cartItems = $cart->items()->with(['options', 'addons'])->where('product_id', $productId)->get();
        
        foreach ($cartItems as $item) {
            // Check if options match
            $itemOptionValues = $item->options->pluck('product_option_value_id')->sort()->values()->toArray();
            $requestOptionValues = collect($optionValues)->sort()->values()->toArray();
            
            if ($itemOptionValues !== $requestOptionValues) {
                continue;
            }
            
            // Check if addons match
            $itemAddons = $item->addons->map(function ($addon) {
                return [
                    'addon_id' => $addon->addon_id,
                    'quantity' => $addon->quantity
                ];
            })->sortBy('addon_id')->values()->toArray();
            
            $requestAddons = collect($addons)->map(function ($addon) {
                return [
                    'addon_id' => $addon['addon_id'],
                    'quantity' => $addon['quantity'] ?? 1
                ];
            })->sortBy('addon_id')->values()->toArray();
            
            if ($itemAddons === $requestAddons) {
                return $item;
            }
        }
        
        return null;
    }

    /**
     * Transform cart data for API response
     *
     * @param Cart $cart
     * @return array
     */
    private function transformCart(Cart $cart)
    {
        return [
            'id' => $cart->id,
            // 'store' => [
            //     'id' => $cart->store->id,
            //     'name_en' => $cart->store->name_en,
            //     'name_ar' => $cart->store->name_ar,
            //     'description_en' => $cart->store->description_en,
            //     'description_ar' => $cart->store->description_ar,
            //     'logo_url' => $cart->store->logo_url,
            //     'status' => $cart->store->status,
            // ],
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name_en' => $item->product->name_en,
                        'name_ar' => $item->product->name_ar,
                        'description_en' => $item->product->description_en,
                        'description_ar' => $item->product->description_ar,
                        'base_price' => $item->product->base_price,
                        'is_active' => $item->product->is_active,
                        'primary_image' => $item->product->primaryImage ? [
                            'id' => $item->product->primaryImage->id,
                            'image_url' => $item->product->primaryImage->image_url,
                        ] : null,
                    ],
                    'quantity' => $item->quantity,
                    'selected_options' => $item->options->map(function ($option) use ($item) {
                        return [
                            'id' => $option->id,
                            'option_group' => [
                                'id' => $option->productOptionValue->productOption->optionGroup->id,
                                'name_en' => $option->productOptionValue->productOption->optionGroup->name_en,
                                'name_ar' => $option->productOptionValue->productOption->optionGroup->name_ar,
                            ],
                            'option_value' => [
                                'id' => $option->productOptionValue->optionValue->id,
                                'value_en' => $option->productOptionValue->optionValue->value_en,
                                'value_ar' => $option->productOptionValue->optionValue->value_ar,
                                'price_type' => $option->productOptionValue->price_type,
                                'price_value' => $option->productOptionValue->price_value,
                                'calculated_price' => $option->productOptionValue->calculatePrice($item->product->base_price),
                            ],
                        ];
                    })->toArray(),
                    'selected_addons' => $item->addons->map(function ($addon) {
                        return [
                            'id' => $addon->id,
                            'addon' => [
                                'id' => $addon->addon->id,
                                'name_en' => $addon->addon->name_en,
                                'name_ar' => $addon->addon->name_ar,
                                'description_en' => $addon->addon->description_en,
                                'description_ar' => $addon->addon->description_ar,
                                'price' => $addon->addon->price,
                            ],
                            'quantity' => $addon->quantity,
                            'total_price' => $addon->total_price,
                        ];
                    })->toArray(),
                    'item_price' => $item->item_price,
                    'item_total' => $item->item_total,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray(),
            'subtotal' => $cart->subtotal,
            'total' => $cart->total,
            'item_count' => $cart->item_count,
            'created_at' => $cart->created_at,
            'updated_at' => $cart->updated_at,
        ];
    }
}
