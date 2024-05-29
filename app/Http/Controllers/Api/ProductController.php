<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductsResource;
use App\Models\AppNotification;
use App\Models\Banner;
use App\Models\Category;
use App\Models\HomePageImage;
use App\Models\LikeProduct;
use App\Models\Order;
use App\Models\Package;
use App\Models\PackagePayment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\ProductVariation;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\UserProductHistory;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\NotiSend;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function showAllProductId(){
        $all_product = Product::has('user')->get('id');
        return response()->json([
            'status' => true, 'Message' => 'Product found',
            'all_product' => $all_product ?? [],
        ], 200);
    }

    public function home($role = null)
    {
        $all_product = [];
        $feature_product = [];
        $discount_product = [];
        $newArrivalProduct = [];
        $topRatingProduct = [];
        $justForYouProduct = [];
        $justForYouSlider = [];
        $trendingProduct = [];
        $bestSeller = [];
        $banner_header = [];
        $banner_body = [];
        $banner_footer = [];
        if ($role == 'retailer') {
            $all_product = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->take(5)->get();
            $feature_product = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_featured', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->take(5)->get();
            $discount_product = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('discount_price', '!=', null)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->take(5)->get();
            $newArrivalProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_new_arrival', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->take(5)->get();
            $topRatingProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->take(5)->get();
            $trendingProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_trending', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->take(5)->get();
            $banner_header = Banner::orderBy('id', 'DESC')->where('is_header', true)->take(5)->get();
            $banner_body = Banner::orderBy('id', 'DESC')->where('is_body', true)->take(5)->get();
            $banner_footer = Banner::orderBy('id', 'DESC')->where('is_footer', true)->take(5)->get();
            $justForYouSlider = HomePageImage::orderBy('id', 'DESC')->where('is_just_for_you', true)->where('is_retailer', true)->where('is_app', true)->take(1)->get();
            $justForYouProduct = HomePageImage::orderBy('id', 'DESC')->where('is_just_for_you', true)->where('is_retailer', true)->where('is_app', true)->skip(1)->take(6)->get();
            $bestSeller = HomePageImage::orderBy('id', 'DESC')->where('is_best_seller', true)->where('is_retailer', true)->take(5)->get();
        }
        if ($role == 'wholesaler') {
            $all_product = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->take(5)->get();
            $feature_product = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_featured', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->take(5)->get();
            $discount_product = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('discount_price', '!=', null)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->take(5)->get();
            $newArrivalProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_new_arrival', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->take(5)->get();
            $topRatingProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->take(5)->get();
            $trendingProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_trending', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->take(5)->get();
            $banner_header = Banner::orderBy('id', 'DESC')->where('is_header', true)->take(5)->get();
            $banner_body = Banner::orderBy('id', 'DESC')->where('is_body', true)->take(5)->get();
            $banner_footer = Banner::orderBy('id', 'DESC')->where('is_footer', true)->take(5)->get();
            $justForYouSlider = HomePageImage::orderBy('id', 'DESC')->where('is_just_for_you', true)->where('is_wholesaler', true)->where('is_app', true)->take(3)->get();
            $justForYouProduct = HomePageImage::orderBy('id', 'DESC')->where('is_just_for_you', true)->where('is_wholesaler', true)->where('is_app', true)->skip(3)->take(6)->get();
            $bestSeller = HomePageImage::orderBy('id', 'DESC')->where('is_best_seller', true)->where('is_wholesaler', true)->take(5)->get();
        }

        return response()->json([
            'status' => true, 'Message' => 'Product found',
            'all_product' => ProductsResource::collection($all_product),
            'feature_product' => ProductsResource::collection($feature_product),
            'discount_product' => ProductsResource::collection($discount_product),
            'newArrivalProduct' => ProductsResource::collection($newArrivalProduct),
            'topRatingProduct' => ProductsResource::collection($topRatingProduct),
            'justForYouProduct' => $justForYouProduct ?? [],
            'justForYouSlider' => $justForYouSlider ?? [],
            'trendingProduct' => $trendingProduct ?? [],
            'bestSeller' => $bestSeller ?? [],
            'banner_header' => $banner_header ?? [],
            'banner_body' => $banner_body ?? [],
            'banner_footer' => $banner_footer ?? [],
        ], 200);
    }

    public function webhome($role = null)
    {
        $all_product = [];
        $feature_product = [];
        $discount_product = [];
        $newArrivalProduct = [];
        $topRatingProduct = [];
        $justForYouProduct = [];
        $trendingProduct = [];
        $bestSeller = [];
        $banner_header = [];
        $banner_body = [];
        $banner_footer = [];
        if ($role == 'retailer') {
            $all_product = HomePageImage::orderBy('id', 'DESC')->where('is_just_for_you', true)->where('is_retailer', true)->take(5)->get();
            $discount_product = HomePageImage::orderBy('id', 'DESC')->where('is_discount', true)->where('is_retailer', true)->take(5)->get();
            $feature_product = HomePageImage::orderBy('id', 'DESC')->where('is_featured', true)->where('is_retailer', true)->take(5)->get();
            $newArrivalProduct = HomePageImage::orderBy('id', 'DESC')->where('is_new_arrival', true)->where('is_retailer', true)->take(5)->get();
            $topRatingProduct = HomePageImage::orderBy('id', 'DESC')->where('is_top_rating', true)->where('is_retailer', true)->take(5)->get();
            $justForYouProduct = HomePageImage::orderBy('id', 'DESC')->where('is_just_for_you', true)->where('is_retailer', true)->where('is_app', false)->skip(3)->take(6)->get();
            $justForYouSlider = HomePageImage::orderBy('id', 'DESC')->where('is_just_for_you', true)->where('is_retailer', true)->where('is_app', false)->take(3)->get();
            $trendingProduct = HomePageImage::orderBy('id', 'DESC')->where('is_trending', true)->where('is_retailer', true)->take(5)->get();
            $bestSeller = HomePageImage::orderBy('id', 'DESC')->where('is_best_seller', true)->where('is_retailer', true)->take(3)->get();
            $banner_header = Banner::orderBy('id', 'DESC')->where('is_header', true)->take(5)->get();
            $banner_body = Banner::orderBy('id', 'DESC')->where('is_body', true)->take(5)->get();
            $banner_footer = Banner::orderBy('id', 'DESC')->where('is_footer', true)->take(5)->get();
        }
        if ($role == 'wholesaler') {
            $all_product = HomePageImage::orderBy('id', 'DESC')->where('is_just_for_you', true)->where('is_wholesaler', true)->take(5)->get();
            $discount_product = HomePageImage::orderBy('id', 'DESC')->where('is_discount', true)->where('is_wholesaler', true)->take(5)->get();
            $feature_product = HomePageImage::orderBy('id', 'DESC')->where('is_featured', true)->where('is_wholesaler', true)->take(5)->get();
            $newArrivalProduct = HomePageImage::orderBy('id', 'DESC')->where('is_new_arrival', true)->where('is_wholesaler', true)->take(5)->get();
            $topRatingProduct = HomePageImage::orderBy('id', 'DESC')->where('is_top_rating', true)->where('is_wholesaler', true)->take(5)->get();
            $justForYouProduct = HomePageImage::orderBy('id', 'DESC')->where('is_just_for_you', true)->where('is_wholesaler', true)->where('is_app', false)->skip(3)->take(6)->get();
            $justForYouSlider = HomePageImage::orderBy('id', 'DESC')->where('is_just_for_you', true)->where('is_wholesaler', true)->where('is_app', false)->take(3)->get();
            $trendingProduct = HomePageImage::orderBy('id', 'DESC')->where('is_trending', true)->where('is_wholesaler', true)->take(5)->get();
            $bestSeller = HomePageImage::orderBy('id', 'DESC')->where('is_best_seller', true)->where('is_wholesaler', true)->take(3)->get();
            $banner_header = Banner::orderBy('id', 'DESC')->where('is_header', true)->take(5)->get();
            $banner_body = Banner::orderBy('id', 'DESC')->where('is_body', true)->take(5)->get();
            $banner_footer = Banner::orderBy('id', 'DESC')->where('is_footer', true)->take(5)->get();
        }

        return response()->json([
            'status' => true, 'Message' => 'Product found',
            'all_product' => $all_product ?? [],
            'feature_product' => $feature_product ?? [],
            'discount_product' => $discount_product ?? [],
            'newArrivalProduct' => $newArrivalProduct ?? [],
            'topRatingProduct' => $topRatingProduct ?? [],
            'justForYouProduct' => $justForYouProduct ?? [],
            'justForYouSlider' => $justForYouSlider ?? [],
            'trendingProduct' => $trendingProduct ?? [],
            'bestSeller' => $bestSeller ?? [],
            'banner_header' => $banner_header ?? [],
            'banner_body' => $banner_body ?? [],
            'banner_footer' => $banner_footer ?? [],
        ], 200);
    }

    public function show($role = null, $skip = 0, $take = 0)
    {
        $all_product = [];
        if ($role == 'retailer') {
            $all_product = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->skip($skip)->take($take)->get();
            $all_product_count = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->count();
        }
        if ($role == 'wholesaler') {
            $all_product = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->skip($skip)->take($take)->get();
            $all_product_count = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->count();
        }
        return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($all_product), 'ProductsCount' => $all_product_count], 200);
    }

    public function wholesalerProducts()
    {
        $all_product = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->whereHas('user', function ($q) {
            $q->whereRelation('role', 'name', 'wholesaler');
        })->get();
        if (count($all_product)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($all_product)], 200);
        else return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $all_product ?? []]);
    }

    public function appWholesalerProducts()
    {
        $wholesalers = User::has('products')->with(['role', 'products.images'])->where('role_id', 4)->get();
        if (count($wholesalers)) return response()->json(['status' => true, 'wholesalers' => $wholesalers ?? []], 200);
        return response()->json(['status' => false, 'Message' => 'not found']);
    }

    public function showAdminProduct(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'skip' => 'required',
            'take' => 'required',
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }
        $skip = $request->skip;
        $take = $request->take;
        $status = $request->status;
        $role = $request->role;
        $search = $request->search;
        $all_product = Product::has('user')->with(['user.role', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('is_delete', false);
        $all_product_count = Product::has('user')->with(['user.role', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('is_delete', false);
        if (!empty($status)) {
            $all_product->where('status', $status);
            $all_product_count->where('status', $status);
        }
        if (!empty($role)) {
            $all_product->whereHas('user', function ($q) use ($role) {
                $q->whereRelation('role', 'name', $role);
            });
            $all_product_count->whereHas('user', function ($q) use ($role) {
                $q->whereRelation('role', 'name', $role);
            });
        }
        if (!empty($search)) {
            $all_product->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%')
                    ->orWhere('discount_price', 'like', '%' . $search . '%')
                    ->orWhere('desc', 'like', '%' . $search . '%')
                    ->orWhere('tags', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
            $all_product_count->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%')
                    ->orWhere('discount_price', 'like', '%' . $search . '%')
                    ->orWhere('desc', 'like', '%' . $search . '%')
                    ->orWhere('tags', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }
        $all_products = $all_product->skip($skip)->take($take)->get();
        $all_products_counts = $all_product_count->count();
        if (count($all_products)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($all_products), 'ProductsCount' => $all_products_counts ?? []], 200);
        else return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $all_products ?? [], 'ProductsCount' => $all_products_counts ?? []]);
    }

    public function showSellerProduct($skip = 0, $take = 0)
    {
        $all_product = [];
        $all_product_count = [];
        if ($skip == 0 && $take == 0) {
            $all_product = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('user_id', auth()->user()->id)->get();
            $all_product_count = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('user_id', auth()->user()->id)
                ->count();
        } else {
            $all_product = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('user_id', auth()->user()->id)
                ->skip($skip)->take($take)->get();
            $all_product_count = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('user_id', auth()->user()->id)
                ->count();
        }
        if (count($all_product)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($all_product), 'ProductsCount' => $all_product_count ?? []], 200);
        else return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $all_product ?? [], 'ProductsCount' => $all_product_count ?? []]);
    }

    public function featuredProduct($role = null, $skip = 0, $take = 0)
    {
        $feature_product = [];
        if ($role == 'retailer') {
            $feature_product = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_featured', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->skip($skip)->take($take)->get();
            $feature_product_count = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_featured', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->count();
        }
        if ($role == 'wholesaler') {
            $feature_product = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_featured', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->skip($skip)->take($take)->get();
            $feature_product_count = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_featured', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->count();
        }
        if (count($feature_product)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($feature_product), 'ProductsCount' => $feature_product_count ?? []], 200);
        else return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $feature_product ?? [], 'ProductsCount' => $feature_product_count ?? []]);
    }

    public function discountProduct($role = null, $skip = 0, $take = 0)
    {
        $discount_product = [];
        if ($role == 'retailer') {
            $discount_product = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('discount_price', '!=', null)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->skip($skip)->take($take)->get();
            $discount_product_count = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('discount_price', '!=', null)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->count();
        }
        if ($role == 'wholesaler') {
            $discount_product = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('discount_price', '!=', null)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->skip($skip)->take($take)->get();
            $discount_product_count = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('discount_price', '!=', null)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->count();
        }
        if (count($discount_product)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($discount_product), 'ProductsCount' => $discount_product_count ?? []], 200);
        else return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $discount_product ?? [], 'ProductsCount' => $discount_product_count ?? []]);
    }

    public function newArrivalProduct($role = null, $skip = 0, $take = 0)
    {
        $newArrivalProduct = [];
        if ($role == 'retailer') {
            $newArrivalProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_new_arrival', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->skip($skip)->take($take)->get();
            $newArrivalProductCount = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_new_arrival', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->count();
        }
        if ($role == 'wholesaler') {
            $newArrivalProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_new_arrival', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->skip($skip)->take($take)->get();
            $newArrivalProductCount = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_new_arrival', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->count();
        }
        if (count($newArrivalProduct)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($newArrivalProduct), 'ProductsCount' => $newArrivalProductCount ?? []], 200);
        else return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $newArrivalProduct ?? [], 'ProductsCount' => $newArrivalProductCount ?? []]);
    }

    public function topRatingProduct($role = null, $skip = 0, $take = 0)
    {
        $topRatingProduct = [];
        if ($role == 'retailer') {
            $topRatingProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->skip($skip)->take($take)->get();
            $topRatingProduct_count = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->count();
        }
        if ($role == 'wholesaler') {
            $topRatingProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->skip($skip)->take($take)->get();
            $topRatingProduct_count = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->count();
        }
        if (count($topRatingProduct)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($topRatingProduct), 'ProductsCount' => $topRatingProduct_count ?? []], 200);
        else return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $topRatingProduct ?? [], 'ProductsCount' => $topRatingProduct_count ?? []]);
    }

    public function trendingProduct($role = null, $skip = 0, $take = 0)
    {
        $trendingProduct = [];
        if ($role == 'retailer') {
            $trendingProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_trending', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->skip($skip)->take($take)->get();
            $trendingProduct_count = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_trending', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'retailer');
            })->count();
        }
        if ($role == 'wholesaler') {
            $trendingProduct = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_trending', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->skip($skip)->take($take)->get();
            $trendingProduct_count = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('is_trending', true)->whereHas('user', function ($q) {
                $q->whereRelation('role', 'name', 'wholesaler');
            })->count();
        }
        if (count($trendingProduct)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($trendingProduct), 'ProductsCount' => $trendingProduct_count ?? []], 200);
        else return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $trendingProduct ?? [], 'ProductsCount' => $trendingProduct_count ?? []]);
    }

    public function vendorProduct(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }
        $all_product = Product::has('user')->with('user', 'images', 'variation', 'subCategories.categories', 'reviews.users')->where('user_id', $request->id)->get();
        if (count($all_product)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($all_product)], 200);
        return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $all_product ?? []]);
    }

    public function showProduct(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }
        $id = explode(',', $request->id);
        $all_product = Product::whereIn('id', $id)->has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->get();
        if (count($all_product)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($all_product)], 200);
        return response()->json(['status' => false, 'Message' => 'Product not found']);
    }

    public function getById($id)
    {
        $all_product = Product::where('id', $id)->has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->first();
        if (!empty($all_product)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => new ProductsResource($all_product)], 200);
        return response()->json(['status' => false, 'Message' => 'Product not found']);
    }

    public function search($name, $role = null)
    {
        if (!empty($name)) {
            $product = [];
            $names = explode(',', $name);
            if ($role == 'retailer') {
                $product = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where(function ($query) use ($names) {
                    foreach ($names as $tag) {
                        $query->where('title', 'LIKE', '%' . $tag . '%')->orWhere('tags', 'LIKE', '%' . $tag . '%');
                    }
                })->whereHas('user', function ($q) {
                    $q->whereRelation('role', 'name', 'retailer');
                })->get();
            } else if ($role == 'wholesaler') {
                $product = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where(function ($query) use ($names) {
                    foreach ($names as $tag) {
                        $query->where('title', 'LIKE', '%' . $tag . '%')->orWhere('tags', 'LIKE', '%' . $tag . '%');
                    }
                })->whereHas('user', function ($q) {
                    $q->whereRelation('role', 'name', 'wholesaler');
                })->get();
            } else {
                $product = Product::where(function ($query) use ($names) {
                    foreach ($names as $tag) {
                        $query->where('title', 'LIKE', '%' . $tag . '%')->orWhere('tags', 'LIKE', '%' . $tag . '%');
                    }
                })->get();
            }
            if (count($product)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($product)], 200);
            else return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $product ?? []]);
        } else return response()->json(['status' => false, 'Message' => 'Parameter is null']);
    }

    public function searchLocation(Request $request)
    {
        $product = [];
        $query = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users']);
        if(isset($request->category) && !empty($request->category)) $query->whereRelation('subCategories.categories', 'id', $request->category);
        if(isset($request->sub_category) && !empty($request->sub_category)) $query->where('sub_category_id',$request->sub_category);
        if(isset($request->price_from) && isset($request->price_to)){
            $query->whereHas('variation', function ($q) use($request) {
                    $q->whereBetween('price', [$request->price_from, $request->price_to]);
            });
        }

        if (!empty($request->lat) && !empty($request->lng) && !empty($request->distance)){
            $latitude  = $request->lat;
            $langitude  = $request->lng;
            $users = User::all();
            $distance = $request->distance;  //(miles - see note)
            $arr_user = [];
            foreach ($users as $user) {
                if(!empty($user->lat) || !empty($user->lng)){
                    $delta_lat = $user->lat - $latitude;
                    $delta_lon = $user->lng - $langitude;

                    $earth_radius = 6372.795477598;

                    $alpha    = $delta_lat / 2;
                    $beta     = $delta_lon / 2;
                    $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($latitude)) * cos(deg2rad($user->lat_from)) * sin(deg2rad($beta)) * sin(deg2rad($beta));
                    $c        = asin(min(1, sqrt($a)));
                    $distance1 = 2 * $earth_radius * $c;
                    $distance1 = $distance1 * 0.621371;
                    if ($distance > $distance1) {
                        $arr_user[] = $user->id;
                    }
                }
            }
            if(empty($arr_user) && count($arr_user) < 0) $arr_user[] = 0;
        }
        if(isset($request->name) && !empty($request->name)){
            $names = explode(',', $request->name);
            foreach ($names as $tag) {
                $query->where('title', 'LIKE', '%' . $tag . '%');
            }
        }
        if ($request->role == 'retailer') {
            $query->whereIn('user_id',$arr_user);
            $product = $query->whereHas('user', function ($q1) {
                $q1->whereRelation('role', 'name', 'retailer');
            })->get();
        } else if ($request->role == 'wholesaler') {
            $query->whereIn('user_id',$arr_user);
            $product = $query->whereHas('user', function ($q1) {
                $q1->whereRelation('role', 'name', 'wholesaler');
            })->get();

        } else {
            $query->whereIn('user_id',$arr_user);
            $product = $query->get();
        }
        if (count($product)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($product)], 200);
        else return response()->json(['status' => false, 'Message' => 'Product not found', 'Products' => $product ?? []]);

    }
    public function add(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required',
            'price' => 'nullable',
            'discount' => 'nullable',
            'product_desc' => 'required',
            'product_image' => 'required|array',
            'variations' => 'required',
            'tags' => 'required',
            'sub_category_id' => 'required',
            // 'brand' => 'required',
            // 'product_status' => 'required',
            // 'product_selected_qty' => 'nullable',
            // 'category' => 'required',
            // 'featured' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if ($user->role->name == 'wholesaler' || $user->role->name == 'retailer') {
                // $payment = PackagePayment::where('user_id', $user->id)->where('end_date', '<', Carbon::now())->first();
                // $payment exist means expired payment;
                // if ($payment) throw new Error("Please buy package!");
                if ($user->is_active == false) throw new Error("You Account status has been deactivated!");
                $productCount = Product::where('user_id', $user->id)->count();
                $packageProductCount = PackagePayment::where('user_id', $user->id)->first();
                if (!$packageProductCount) throw new Error("Please buy package!");
                $qty = $packageProductCount->updated_product_qty;
                if ($productCount >= $qty) throw new Error("Your Product limit is full now you buy new package!");
                $new_product = new Product();
                $new_product->user_id = $user->id;
                $new_product->sub_category_id = $request->sub_category_id;
                $new_product->title = $request->title;
                $new_product->price = $request->price ?? 0;
                $new_product->discount_price = $request->discount ?? 0;
                $new_product->color = $request->color;
                $new_product->tags = json_encode($request->tags);
                $new_product->desc = $request->product_desc;
                $new_product->is_featured = $request->featured ?? false;
                if (!$new_product->save()) throw new Error("Product not added!");
                if (!empty($request->product_image)) {
                    foreach ($request->product_image as $image) {
                        $product_image = new ProductImage();
                        $product_image->product_id = $new_product->id;
                        $filename = "Product-" . time() . "-" . rand() . "." . $image->getClientOriginalExtension();
                        $image->storeAs('product', $filename, "public");
                        $product_image->image = "product/" . $filename;
                        if (!$product_image->save()) throw new Error("Product Images not added!");
                    }
                }
                if (!empty($request->variations)) {
                    foreach ($request->variations as $variation) {
                        if (is_object($variation)) $variation = $variation->toArray();
                        $newVariation = new ProductVariation();
                        $newVariation->product_id = $new_product->id;
                        $newVariation->size = $variation['size'];
                        $newVariation->stock = $variation['stock'];
                        $newVariation->price = $variation['price'];
                        if (!$newVariation->save()) throw new Error("Product Variations not added!");
                    }
                }
                $products = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('id', $new_product->id)->first();
                $user = User::whereRelation('role', 'name', 'admin')->first();
                $title = 'NEW PRODUCT';
                $message = 'You have recieved new product';
                $appnot = new AppNotification();
                $appnot->user_id = $user->id;
                $appnot->notification = $message;
                $appnot->navigation = $title;
                $appnot->save();
                NotiSend::sendNotif($user->device_token, '', $title, $message);
                DB::commit();
                return response()->json(['status' => true, 'Message' => 'Product Added Successfully!', 'Products' => new ProductsResource($products) ?? []], 200);
            } else throw new Error("Authenticated User Required!");
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'Message' => $th->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required',
            'title' => 'required',
            'price' => 'nullable',
            'discount' => 'nullable',
            'product_desc' => 'required',
            // 'product_image' => 'required|array',
            'variations' => 'required',
            'tags' => 'required',
            'sub_category_id' => 'required',
            // 'brand' => 'required',
            // 'product_status' => 'required',
            // 'product_selected_qty' => 'nullable',
            // 'category' => 'required',
            // 'featured' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if ($user->role->name == 'wholesaler' || $user->role->name == 'retailer') {
                // $payment = PackagePayment::where('user_id', $user->id)->where('end_date', '<', Carbon::now())->first();
                // // $payment exist means expired payment;
                // if ($payment || $user->is_active == false) throw new Error("Please buy package!");
                $productCount = Product::where('user_id', $user->id)->count();
                $packageProductCount = PackagePayment::where('user_id', $user->id)->first();
                $qty = $packageProductCount->updated_product_qty;
                if ($productCount >= $qty) throw new Error("Your Product limit is full now you buy new package!");
                $product = Product::where('id', $request->id)->first();
                $product->user_id = $user->id;
                $product->sub_category_id = $request->sub_category_id;
                $product->title = $request->title;
                $product->price = $request->price;
                $product->discount_price = $request->discount;
                $product->tags = json_encode($request->tags);
                $product->desc = $request->product_desc;
                $product->status = 'pending';
                $product->is_active = false;
                $product->is_featured = $request->featured ?? false;
                if (!$product->save()) throw new Error("Product not Updated!");
                if (!empty($request->product_image)) {
                    foreach ($request->product_image as $image) {
                        $product_image = new ProductImage();
                        $product_image->product_id = $product->id;
                        $filename = "Product-" . time() . "-" . rand() . "." . $image->getClientOriginalExtension();
                        $image->storeAs('product', $filename, "public");
                        $product_image->image = "product/" . $filename;
                        if (!$product_image->save()) throw new Error("Product Images not added!");
                    }
                }
                if (!empty($request->variations)) {
                    $existVariation = ProductVariation::where('product_id', $product->id)->get();
                    if (!empty($existVariation)) {
                        foreach ($existVariation as $key => $value) {
                            $value->delete();
                        }
                    }
                    foreach ($request->variations as $variation) {
                        if (is_object($variation)) $variation = $variation->toArray();
                        $newVariation = new ProductVariation();
                        $newVariation->product_id = $product->id;
                        $newVariation->size = $variation['size'];
                        $newVariation->stock = $variation['stock'];
                        $newVariation->price = $variation['price'];
                        if (!$newVariation->save()) throw new Error("Product Variations not added!");
                    }
                }
                $products = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('id', $product->id)->first();
                DB::commit();
                return response()->json(['status' => true, 'Message' => 'Product Updated Successfully!', 'Products' => new ProductsResource($products) ?? []], 200);
            } else throw new Error("Authenticated User Required!");
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'Message' => $th->getMessage()]);
        }
    }

    public function image($id)
    {
        $all_image = ProductImage::where('product_id', $id)->get();
        if (count($all_image)) return response()->json(['status' => true, 'Message' => 'Product Image found', 'Images' => $all_image], 200);
        else return response()->json(['status' => false, 'Message' => 'Product Image not found']);
    }

    public function delete(Request $request)
    {
        $product = Product::where('id', $request->id)->first();
        if (!empty($product)) {
            if ($product->is_delete == false) $product->is_delete = true;
            else $product->is_delete = false;
            if ($product->save()) return response()->json(['status' => true, 'Message' => 'Successfully deleted Product'], 200);
        } else {
            return response()->json(["status" => false, 'Message' => 'Product not deleted']);
        }
    }

    public function addImage(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'product_id' => 'required|numeric',
            'product_image' => 'required|array',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }
        if (!empty($request->product_image)) {
            foreach ($request->product_image as $image) {
                $product_image = new ProductImage();
                $product_image->product_id = $request->product_id;
                $filename = "Product-" . time() . "-" . rand() . "." . $image->getClientOriginalExtension();
                $image->storeAs('product', $filename, "public");
                $product_image->image = "product/" . $filename;
                $product_image->save();
            }
            return response()->json(['status' => true, 'Message' => 'Product Image Added Successfully!'], 200);
        } else return response()->json(['status' => false, 'Message' => 'Product Image not Added!']);
    }

    public function deleteImage(Request $request)
    {
        $product = ProductImage::where('id', $request->id)->first();
        if (!empty($product)) {
            if ($product->delete()) return response()->json(['status' => true, 'Message' => 'Successfully Image deleted'], 200);
        } else return response()->json(["status" => false, 'Message' => 'Unsuccessfull Image deleted']);
    }

    public function showDeleteProduct(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'skip' => 'required',
            'take' => 'required',
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }
        $skip = $request->skip;
        $take = $request->take;
        $role = $request->role;
        $search = $request->search;
        $product = Product::has('user')->with(['user.role', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('is_delete', true);
        $product_count = Product::has('user')->with(['user.role', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('is_delete', true);
        if (!empty($role)) {
            $product->whereHas('user', function ($q) use ($role) {
                $q->whereRelation('role', 'name', $role);
            });
            $product_count->whereHas('user', function ($q) use ($role) {
                $q->whereRelation('role', 'name', $role);
            });
        }
        if (!empty($search)) {
            $product->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%')
                    ->orWhere('discount_price', 'like', '%' . $search . '%')
                    ->orWhere('desc', 'like', '%' . $search . '%')
                    ->orWhere('tags', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
            $product_count->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%')
                    ->orWhere('discount_price', 'like', '%' . $search . '%')
                    ->orWhere('desc', 'like', '%' . $search . '%')
                    ->orWhere('tags', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }
        $products = $product->skip($skip)->take($take)->get();
        $products_counts = $product_count->count();
        if (count($products)) return response()->json(['status' => true, 'Message' => 'Successfully Show Deleted Products', 'Products' => ProductsResource::collection($products), 'ProductsCount' => $products_counts ?? []], 200);
        else return response()->json(["status" => false, 'Message' => 'Products not found', 'Products' => $products ?? [], 'ProductsCount' => $products_counts ?? []]);
    }

    public function hardDelete($id)
    {
        if (empty($id)) return response()->json(["status" => false, 'Message' => 'Id not found']);
        $product = Product::where('id', $id)->where('is_delete', true)->first();
        if (!empty($product)) {
            if ($product->delete()) return response()->json(['status' => true, 'Message' => 'Successfully deleted Product'], 200);
        } else {
            return response()->json(["status" => false, 'Message' => 'Product not deleted']);
        }
    }

    public function allHardDelete()
    {
        $product = Product::where('is_delete', true)->get();
        if (count($product)) {
            foreach ($product as $key => $value) {
                if ($value->delete());
            }
            return response()->json(['status' => true, 'Message' => 'Successfully hard deleted Product'], 200);
        } else {
            return response()->json(["status" => false, 'Message' => 'Product not found']);
        }
    }

    public function statusChangeProduct(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
            'message' => 'nullable',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }
        $product = Product::where('id', $request->id)->first();
        if (empty($product)) return response()->json(["status" => false, 'Message' => 'Product not Found']);
        $product->status = $request->status;
        if ($product->save()) {
            $user = $product->user;
            if ($product->status == 'approved') {
                $title = 'YOUR PRODUCT HAS BEEN APPROVED';
                $message = 'Dear ' . $user->username . ' your product has been approved from admin-The Real Bazaar';
                $appnot = new AppNotification();
                $appnot->user_id = $user->id;
                $appnot->notification = $message;
                $appnot->navigation = $title;
                $appnot->save();
                NotiSend::sendNotif($user->device_token, '', $title, $message);
                return response()->json(["status" => true, 'Message' => 'Product Status Change to Approved Successfully'], 200);
            } elseif ($product->status == 'rejected') {
                $title = 'YOUR PRODUCT HAS BEEN REJECTED';
                $appnot = new AppNotification();
                $appnot->user_id = $user->id;
                $appnot->notification = $request->message;
                $appnot->navigation = $title;
                $appnot->save();
                NotiSend::sendNotif($user->device_token, '', $title, $request->message);
                return response()->json(["status" => true, 'Message' => 'Product Status Change to Rejected Successfully'], 200);
            } else {
                $title = 'YOUR PRODUCT HAS BEEN PENDING';
                $message = 'Dear ' . $user->username . ' your product has been pending from admin-The Real Bazaar';
                $appnot = new AppNotification();
                $appnot->user_id = $user->id;
                $appnot->notification = $message;
                $appnot->navigation = $title;
                $appnot->save();
                NotiSend::sendNotif($user->device_token, '', $title, $message);
                return response()->json(["status" => true, 'Message' => 'Product Status Change to Pending Successfully'], 200);
            }
        } else return response()->json(["status" => false, 'Message' => 'Product Status Change not Successfully']);
    }

    public function productStatusChange(Request $request)
    {
        $product = Product::where('id', $request->id)->first();
        if (!empty($product)) {
            if ($product->is_active == false) $product->is_active = true;
            else $product->is_active = false;
            if ($product->save()) return response()->json(['status' => true, 'Message' => 'Successfully status change Product'], 200);
        } else return response()->json(["status" => false, 'Message' => 'Product Status not change']);
    }

    public function productStatusTrending($id)
    {
        if (empty($id)) return response()->json(['status' => false, 'Message' => 'Id not found']);
        $trending = Product::where('id', $id)->first();
        if (empty($trending)) return response()->json(['status' => false, 'Message' => 'Trending not found']);
        if ($trending->is_trending == false) $trending->is_trending = true;
        else $trending->is_trending = false;
        if ($trending->save()) return response()->json(['status' => true, 'Message' => 'Trending save', 'Product' => new ProductsResource($trending)], 200);
        else return response()->json(['status' => false, 'Message' => 'Trending not save']);
    }

    public function likeProduct(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }

        $likeExist = LikeProduct::where('user_id', auth()->user()->id)->where('product_id', $request->product_id)->first();
        if (is_object($likeExist)) {
            if ($likeExist->delete()) return response()->json(['status' => true, 'Message' => "UnLike Successfully"], 200);
            return response()->json(['status' => false, 'Message' => "UnLike not Successfull"]);
        }
        $like = new LikeProduct();
        $like->user_id = auth()->user()->id;
        $like->product_id = $request->product_id;
        if ($like->save()) return response()->json(['status' => true, 'Message' => "Like Successfully"], 200);
        return response()->json(['status' => false, 'Message' => "Like not Successfull"]);
    }

    public function reviewProduct(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'product_id' => 'required',
            'stars' => 'required|lt:6',
            'comments' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }

        $review = new ProductReview();
        $review->user_id = auth()->user()->id;
        $review->product_id = $request->product_id;
        $review->stars = $request->stars;
        $review->comments = $request->comments;
        if ($review->save()) {
            $products = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->where('id', $request->product_id)->first();

            return response()->json(['status' => true, 'Message' => "Review Successfully", 'Products' => new ProductsResource($products) ?? []], 200);
        }
        return response()->json(['status' => false, 'Message' => "Review not Successfull"]);
    }

    public function historyProduct()
    {
        $historyProduct = Product::has('user')->with(['user', 'images', 'variation', 'subCategories.categories', 'reviews.users'])->whereHas('history', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->get();
        if (count($historyProduct)) return response()->json(['status' => true, 'Message' => 'Product found', 'Products' => ProductsResource::collection($historyProduct)], 200);
        return response()->json(['status' => false, 'Message' => 'Product not found']);
    }

    public function addHistoryProduct(Request $request)
    {
        $valid = Validator::make($request->all(), [
            // 'user_id' => 'required',
            'product_id' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => false, 'Message' => 'Validation errors', 'errors' => $valid->errors()]);
        }

        $history = UserProductHistory::where('user_id', auth()->user()->id)->where('product_id', $request->product_id)->first();
        if (!empty($history)) return response()->json(['status' => true, 'Message' => 'Users product exist in history'], 200);
        $product = new UserProductHistory();
        $product->user_id = auth()->user()->id;
        $product->product_id = $request->product_id;
        if ($product->save()) return response()->json(['status' => true, 'Message' => 'Users product added in history'], 200);
        return response()->json(['status' => false, 'Message' => 'Users product not added in history']);
    }

    public function seller_totalsales_count()
    {
        $seller_totalsales_count = Order::where('seller_id', auth()->user()->id)->groupBy('seller_id')
            ->selectRaw('seller_id,sum(net_amount) AS net_amount')->get();

        $seller_todaysales_count = Order::where('seller_id', auth()->user()->id)
            ->where('order_date', Carbon::today())
            ->selectRaw('seller_id, sum(net_amount) AS net_amount')->groupBy('seller_id')->get();

        $submonth = Carbon::now();
        $subweek = Carbon::now();

        $seller_lastmonthsales_count = Order::where('seller_id', auth()->user()->id)
            ->where('order_date', '>=', $submonth->submonth())
            ->where('order_date', '<=', Carbon::today())
            ->selectRaw('seller_id, sum(net_amount) AS net_amount')->groupBy('seller_id')->get();
        $seller_lastweeksales_count = Order::where('seller_id', auth()->user()->id)
            ->where('order_date', '>=', $subweek->subweek())
            ->where('order_date', '<=', Carbon::today())
            ->selectRaw('seller_id, sum(net_amount) AS net_amount')->groupBy('seller_id')->get();
        return response()->json(["status" => true, 'totalsales_count' => $seller_totalsales_count, 'lastmonthsales_count' => $seller_lastmonthsales_count, 'todaysales_count' => $seller_todaysales_count, 'lastweeksales_count' => $seller_lastweeksales_count], 200);
    }

    public function seller_products_count()
    {
        $seller_products_count = Product::where('user_id', auth()->user()->id)->count();
        $seller_category_count = SubCategory::with('categories:id,name')->withCount('products')->get();
        return response()->json([
            "status" => true, 'products_count' => $seller_products_count,
            'category_count' => $seller_category_count
        ], 200);
    }

    public function seller_top_products()
    {
        $seller_top_products = Product::where('user_id', auth()->user()->id)->withCount('orders')->get();
        $seller_top_products = $seller_top_products->sortByDesc('orders_count')->values();
        return response()->json(["status" => true, 'seller_top_products' => $seller_top_products], 200);
    }

    public function seller_top_customers()
    {
        $seller_top_customers = Order::selectRaw('user_id, SUM(net_amount) as total_amount')->with('users')->where('seller_id', auth()->user()->id)->groupBy('user_id')->get();
        $seller_top_customers = $seller_top_customers->sortByDesc('total_amount')->values();
        return response()->json(["status" => true, 'seller_top_customers' => $seller_top_customers], 200);
    }

    public function admin_totalsales_count()
    {
        $seller_totalsales_count = Payment::selectRaw('sum(total) AS total')->get();

        $seller_todaysales_count = Payment::whereDate('created_at', Carbon::today())
            ->selectRaw('sum(total) AS total')->get();

        $submonth = Carbon::now();
        $subweek = Carbon::now();

        $seller_lastmonthsales_count = Payment::where('created_at', '>=', $submonth->submonth())
            ->where('created_at', '<=', Carbon::today())
            ->selectRaw('sum(total) AS total')->get();

        $seller_lastweeksales_count = Payment::where('created_at', '>=', $subweek->subweek())
            ->where('created_at', '<=', Carbon::today())
            ->selectRaw('sum(total) AS total')->get();

        return response()->json(["status" => true, 'totalsales_count' => $seller_totalsales_count, 'lastmonthsales_count' => $seller_lastmonthsales_count, 'todaysales_count' => $seller_todaysales_count, 'lastweeksales_count' => $seller_lastweeksales_count], 200);
    }

    public function admin_vendor_count()
    {
        $vendor_count = User::whereHas('role', function ($query) {
            $query->where('name', 'seller');
        })->count();
        $vendor_product_count = User::withCount('products')->get();
        $vendor_product_count = $vendor_product_count->sortByDesc('products_count')->values();
        return response()->json([
            "status" => true, 'vendors_count' => $vendor_count,
            'vendor_products_count' => $vendor_product_count
        ], 200);
    }

    public function seller_top_sales($role = null)
    {
        if ($role == 'wholesaler') {
            $seller_top_sales = User::withCount('sellers_orders_products')
                ->whereHas('role', function ($query) {
                    $query->where('name', 'wholesaler');
                })->get();
        } elseif ($role == 'retailer') {
            $seller_top_sales = User::withCount('sellers_orders_products')
                ->whereHas('role', function ($query) {
                    $query->where('name', 'retailer');
                })->get();
        } else {
            $seller_top_sales = User::withCount('sellers_orders_products')
                ->whereHas('role', function ($query) {
                    $query->where('name', 'retailer')->orWhere('name', 'wholesaler');
                })->get();
        }
        $seller_top_sales = $seller_top_sales->sortByDesc('sellers_orders_products_count')->take(10)->values();
        if (count($seller_top_sales)) return response()->json(["status" => true, 'seller_top_sales' => $seller_top_sales ?? []], 200);
        else return response()->json(["status" => false, 'seller_top_sales' => $seller_top_sales ?? []]);
    }

    public function admin_customer_count()
    {
        $customer_count = User::whereHas('role', function ($query) {
            $query->where('name', 'user');
        })->count();
        $top_customers = Order::selectRaw('user_id, SUM(net_amount) as total_amount')
            ->with('users')->groupBy('user_id')->get();
        $top_customers = $top_customers->sortByDesc('total_amount')->values();
        return response()->json(["status" => true, 'customers_count' => $customer_count, 'top_customers' => $top_customers], 200);
    }

    public function seller_line_chart()
    {
        $lineChart = Order::where('seller_id', auth()->user()->id)
            ->selectRaw("COUNT(*) as orders")
            ->selectRaw("sum(net_amount) as total_amount")
            ->selectRaw("MONTHNAME(created_at) as month_name")
            ->selectRaw("DATE(created_at) as date")
            ->selectRaw('max(created_at) as createdAt')
            ->whereMonth('created_at', date('m'))
            ->groupBy('month_name')
            ->groupBy('date')
            ->orderBy('createdAt')
            ->get();
        return response()->json(["status" => true, 'lineChart' => $lineChart], 200);
    }
}
