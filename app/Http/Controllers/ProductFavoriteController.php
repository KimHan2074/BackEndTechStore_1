<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProductFavoriteService;
use App\Models\User;
use App\Models\ProductFavorite;
use Illuminate\Http\Request;


class ProductFavoriteController extends Controller
{
    protected $productFavoriteService;

    public function __construct(ProductFavoriteService $productFavoriteService)
    {
        $this->productFavoriteService = $productFavoriteService;
    }

    /**
     * Lấy danh sách sản phẩm yêu thích theo userId truyền vào từ FE.
     */
    public function getUserFavorites($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $wishlist = $this->productFavoriteService->getUserWishlist($id);

        return response()->json($wishlist, 200);
    }
    public function destroy($id)
    {
        $favorite = ProductFavorite::find($id);
        if (!$favorite) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $favorite->delete();

        return response()->json(['message' => 'Item removed']);
    }
    public function add(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'product_id' => 'required|exists:products,id',
    ]);

    // Kiểm tra nếu đã có rồi thì không thêm lại
    $exists = ProductFavorite::where('user_id', $request->user_id)
        ->where('product_id', $request->product_id)
        ->exists();

    if ($exists) {
        return response()->json(['message' => 'Đã có trong wishlist'], 200);
    }

    ProductFavorite::create([
        'user_id' => $request->user_id,
        'product_id' => $request->product_id,
    ]);

    return response()->json(['message' => 'Đã thêm vào wishlist'], 201);
}
}

