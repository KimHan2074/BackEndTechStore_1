<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductCart;
use App\Models\ProductFavorite;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;

class ProductRepository
{
    public function getProductsByPromotionType()
    {
        $hotProducts = $this->getProductsByType('hot', 3);
        $newProducts = $this->getProductsByType('new', 3);
        $summerSaleProducts = $this->getProductsByType('summer sale', 3);
        $bestDealProducts = $this->getProductsByType('best deal', 3);
        $featureProducts = $this->getFeaturedProducts(['featured product'], 10);


        return $hotProducts
            ->merge($newProducts)
            ->merge($summerSaleProducts)
            ->merge($bestDealProducts)
            ->merge($featureProducts);
    }

    public function getProductsByType($type, $limit = 3)
    {
        return Product::where('promotion_type', $type)
            ->with(['images', 'reviews'])
            ->take($limit)
            ->get()
            ->map(function ($product) {
                $product->rating = round($product->reviews->avg('rating'), 1) ?? 0;
                $product->image_url = $product->images->first()->image_url ?? null;
                return $product;
            });
    }

    public function getFeaturedProducts(array $types = ['featured product', 'best deal'], $limit = 10)
    {
        return Product::with(['images', 'category', 'reviews'])
            ->whereIn('promotion_type', $types)
            ->take($limit)
            ->get()
            ->map(function ($product) {
                $product->rating = round($product->reviews->avg('rating'), 1) ?? 0;
                $product->image_url = $product->images->first()->image_url ?? null;
                return $product;
            });
    }

    public function getAllWithImages()
    {
        return Product::with('images')->get()->transform(function ($product) {
            $product->image_url = $product->images->first()->image_url ?? null;
            return $product;
        });
    }

    public function getProductCategories()
    {
        return Product::with('category')
            ->get()
            ->pluck('category')
            ->unique('id')
            ->values();
    }

    public function getCartItemsByUser($userId)
    {
        return ProductCart::with(['product', 'product.firstImage']) // <-- thêm 'product' để có stock
            ->where('user_id', $userId)
            ->get();
    }

    public function findWithProduct($cartId)
    {
        return ProductCart::with('product')->find($cartId);
    }

    public function updateQuantity($cartId, $quantity)
    {
        return ProductCart::where('id', $cartId)->update(['quantity' => $quantity]);
    }

    public function deleteCartItem($id)
    {
        return ProductCart::where('id', $id)->delete();
    }

    public function deleteCartItemsByUserId($userId)
    {
        return ProductCart::where('user_id', $userId)->delete();
    }

    public function getValidCoupon(string $code)
    {
        $now = Carbon::today();

        return Coupon::where('code', $code)
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->first();
    }

    public function createOrder($data)
    {
        return Order::create($data);
    }

    public function createOrderDetail($orderId, $item)
    {
        return OrderDetail::create([
            'order_id' => $orderId,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
        ]);
    }

    public function decrementStock($productId, $qty)
    {
        Product::where('id', $productId)->decrement('stock', $qty);
    }

    public function findProductById($productId)
    {
        return Product::find($productId);
    }

    public function addOrUpdateCart($userId, $productId, $quantity)
    {
        $cartItem = ProductCart::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
            return $cartItem;
        }

        return ProductCart::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public function deleteCartItems($userId, array $cartItemIds)
    {
        return ProductCart::where('user_id', $userId)
            ->whereIn('id', $cartItemIds)
            ->delete();
    }

    public function getAllProductsWithImages()
    {
        return Product::with('images')->get();
    }

    public function getTopFiveProducts()
    {
        return Product::with('images') 
            ->orderBy('created_at', 'desc') 
            ->take(5)
            ->get();
    }

    public function getProductWithImagesAndColors(int $productId)
    {
        $product = Product::with(['images', 'colors'])->findOrFail($productId);
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'old_price' => $product->old_price,
            'stock' => $product->stock,
            'colors' => $product->colors->pluck('color'),
            'images' => $product->images->take(4)->pluck('image_url'),
            'category_id' => $product->category_id,
        ];
    }

    public function find($productId)
    {
        return Product::findOrFail($productId);
    }

    public function getRelatedProducts($categoryId, $excludeProductId)
    {
        return Product::with(['firstImage', 'reviews'])
            ->where('category_id', $categoryId)
            ->where('id', '!=', $excludeProductId)
            ->take(6)
            ->get()
            ->map(function ($product) {
                $avgRating = $product->reviews->avg('rating');

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'old_price' => $product->old_price,
                    'promotion_type' => $product->promotion_type,
                    'image' => $product->firstImage ? $product->firstImage->image_url : null,
                    'rating' => round($avgRating, 1),
                ];
            });
    }

    public function addToCart(int $userId, int $productId, int $quantity, ?string $color)
    {
        $cartItem = ProductCart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('color', $color)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
            return $cartItem;
        }

        return ProductCart::create([
            'user_id'    => $userId,
            'product_id' => $productId,
            'quantity'   => $quantity,
            'color'      => $color,
        ]);
    }

    public function addToWishlist(int $userId, int $productId, ?string $color)
    {
        return ProductFavorite::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
            'color' => $color,
        ]);
    }

    public function removeFromWishlist(int $userId, int $productId, ?string $color)
    {
        return ProductFavorite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->when($color, function ($query) use ($color) {
                $query->where('color', $color);
            })
            ->delete();
    }

    public function createOrderImmediately(int $userId, int $productId, int $quantity, ?string $color)
    {
        return DB::transaction(function () use ($userId, $productId, $quantity, $color) {
            $product = Product::findOrFail($productId);

            $order = Order::create([
                'user_id' => $userId,
                'order_date' => now(),
                'status' => 'pending',
                'shipping_option' => 'free',
                'total_amount' => $product->price * $quantity,
                'coupon_code' => null,
                'discount' => 0.00,
                'fullname' => null,
                'phone' => null,
                'address' => null,
                'province' => null,
                'district' => null,
                'ward' => null,
            ]);

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'color' => $color,
            ]);

            return [
                'message' => 'Buy Now order created successfully',
                'order_id' => $order->id,
            ];
        });
    }

    public function getCartItem(int $userId, int $productId, ?string $color = null)
{
    return ProductCart::where('user_id', $userId)
        ->where('product_id', $productId)
        ->when($color, function ($query) use ($color) {
            return $query->where('color', $color);
        })
        ->first();
}

public function getProductsByCategoryId($categoryId)
    {
        return Product::with(['category', 'images'])
            ->where('category_id', $categoryId)
            ->get();
    }

    public function getProductsGroupedByCategory()
    {
        return Category::with(['products.images'])->get();
    }
    public function getAllProducts()
    {
        return Product::all();
    }

    public function deleteProduct(int $productId): bool
    {
        return Product::destroy($productId) > 0;
    }




 public function findById($id)
    {
        return Product::with('category', 'images')->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product->fresh();
    }

   public function createProductManagement(array $data)
{
    $imageUrl = $data['image_url'] ?? null;
    unset($data['image_url']);

    $product = Product::create($data);
    if ($imageUrl) {
      dd($product->images()->create([
    'image_url' => $imageUrl
]));
    }

    $product->load('images');

    return $product;
}

    // Get product of promotion type
    public function getUniquePromotionTypes()
    {
        return Product::select('promotion_type')
            ->distinct()
            ->whereNotNull('promotion_type')
            ->pluck('promotion_type');
    }
}
