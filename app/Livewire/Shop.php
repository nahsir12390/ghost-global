<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Shop extends Component
{
    use WithPagination;

    private const CATEGORY_CACHE_TTL_MINUTES = 15;

    public $search = '';
    public $category = '';
    public $minPrice = 0;
    public $maxPrice = 100000;
    public $sortBy = 'latest';
    public $perPage = 12;
    public $cart = [];
    public $wishlistItems = []; // Store wishlist status for each product
    public $updatingCart = []; // Track which products are being updated
    public $addingToCart = []; // Track which products are being added to cart
    public $updatingWishlist = []; // Track which products are being updated in wishlist

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 100000],
        'sortBy' => ['except' => 'latest'],
    ];

    protected $listeners = [
        'cartUpdated' => 'loadCart',
        'wishlist-updated' => 'loadWishlistStatus'
    ];

    public function mount()
    {
        $this->loadCart();
        $this->loadWishlistStatus();
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
    }

    public function loadWishlistStatus()
    {
        $this->wishlistItems = [];
        
        if (Auth::check()) {
            $wishlist = Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->flip();
            
            foreach ($wishlist as $productId => $value) {
                $this->wishlistItems[$productId] = true;
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingMinPrice()
    {
        $this->resetPage();
    }

    public function updatingMaxPrice()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function getCartQuantity($productId)
    {
        return $this->cart[$productId]['quantity'] ?? 0;
    }

    public function isInCart($productId)
    {
        return isset($this->cart[$productId]) && $this->cart[$productId]['quantity'] > 0;
    }

    public function isInWishlist($productId)
    {
        return $this->wishlistItems[$productId] ?? false;
    }

    public function addToCart($productId)
    {
        $this->addingToCart[$productId] = true;
        
        try {
            $product = Product::with('vendor')->find($productId);
            
            if (!$product) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Product not found!'
                ]);
                return;
            }

            if (! $product->isPurchasable()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => $product->unavailableReason() ?: 'This product is currently unavailable.'
                ]);
                return;
            }

            $cart = session()->get('cart', []);
            
            if (isset($cart[$productId])) {
                // Check if we can add more items
                if ($cart[$productId]['quantity'] >= $product->quantity) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Only ' . $product->quantity . ' items in stock!'
                    ]);
                    return;
                }
                $cart[$productId]['quantity']++;
            } else {
                $cart[$productId] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                    'image' => $product->images[0] ?? null,
                    'slug' => $product->slug,
                ];
            }
            
            session()->put('cart', $cart);
            $this->cart = $cart;
            
            $this->dispatch('cartUpdated');
            $this->dispatch('notify', 
                message: 'Added to cart!', 
                type: 'success'
            );
        } finally {
            unset($this->addingToCart[$productId]);
        }
    }

    public function incrementQuantity($productId)
    {
        $this->updatingCart[$productId] = true;
        
        try {
            $product = Product::with('vendor')->find($productId);
            
            if (!$product) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Product not found!'
                ]);
                return;
            }

            if (! $product->isPurchasable()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => $product->unavailableReason() ?: 'This product is currently unavailable.'
                ]);
                return;
            }

            $cart = session()->get('cart', []);
            
            if (isset($cart[$productId])) {
                // Check stock limit
                if ($cart[$productId]['quantity'] >= $product->quantity) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Only ' . $product->quantity . ' items in stock!'
                    ]);
                    return;
                }
                
                $cart[$productId]['quantity']++;
                session()->put('cart', $cart);
                $this->cart = $cart;
                
                $this->dispatch('cartUpdated');
                $this->dispatch('notify', 
                    message: 'Quantity updated!', 
                    type: 'success'
                );
            }
        } finally {
            unset($this->updatingCart[$productId]);
        }
    }

    public function decrementQuantity($productId)
    {
        $this->updatingCart[$productId] = true;
        
        try {
            $cart = session()->get('cart', []);
            
            if (isset($cart[$productId])) {
                if ($cart[$productId]['quantity'] <= 1) {
                    $this->removeFromCart($productId);
                    return;
                }
                
                $cart[$productId]['quantity']--;
                session()->put('cart', $cart);
                $this->cart = $cart;
                
                $this->dispatch('cartUpdated');
                $this->dispatch('notify', 
                    message: 'Quantity updated!', 
                    type: 'success'
                );
            }
        } finally {
            unset($this->updatingCart[$productId]);
        }
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
            $this->cart = $cart;
            
            $this->dispatch('cartUpdated');
            $this->dispatch('notify', 
                message: 'Item removed from cart!', 
                type: 'success'
            );
        }
    }

    public function toggleWishlist($productId)
    {
        if (!Auth::check()) {
            $this->dispatch('notify', 
                message: 'Please login to add items to your wishlist.', 
                type: 'error'
            );
            return;
        }

        $this->updatingWishlist[$productId] = true;
        
        try {
            $product = Product::find($productId);
            
            if (!$product) {
                $this->dispatch('notify', 
                    message: 'Product not found!', 
                    type: 'error'
                );
                return;
            }

            // Check if already in wishlist
            $existingWishlist = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->first();
            
            if ($existingWishlist) {
                // Remove from wishlist
                $existingWishlist->delete();
                $this->wishlistItems[$productId] = false;
                $this->dispatch('notify', 
                    message: 'Removed from wishlist!', 
                    type: 'success'
                );
            } else {
                // Add to wishlist
                Wishlist::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId
                ]);
                $this->wishlistItems[$productId] = true;
                $this->dispatch('notify', 
                    message: 'Added to wishlist!', 
                    type: 'success'
                );
            }
            
            // Dispatch event to update wishlist counter
            $this->dispatch('wishlistUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                message: 'Wishlist feature is not available right now. Please try again later.',
                type: 'error'
            );
        } finally {
            unset($this->updatingWishlist[$productId]);
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->minPrice = 0;
        $this->maxPrice = 100000;
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        $categories = Cache::remember(
            'shop_sidebar_categories',
            now()->addMinutes(self::CATEGORY_CACHE_TTL_MINUTES),
            fn () => Category::query()
                ->select(['id', 'name', 'slug', 'is_active'])
                ->where('is_active', true)
                ->withCount([
                    'products as products_count' => fn ($query) => $query->where('is_active', true),
                ])
                ->orderBy('name')
                ->get()
        );

        $products = Product::query()
            ->select([
                'id',
                'vendor_id',
                'category_id',
                'name',
                'slug',
                'description',
                'price',
                'compare_price',
                'quantity',
                'images',
                'is_active',
                'created_at',
            ])
            ->with([
                'category:id,name,slug',
                'vendor:id,name,role,store_name,store_slug,vendor_is_active,verified_at,verification_status',
            ])
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where(function ($searchQuery) {
                    $term = '%' . trim($this->search) . '%';

                    $searchQuery->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->when($this->category, function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('slug', $this->category);
                });
            })
            ->when($this->minPrice > 0, function ($query) {
                $query->where('price', '>=', $this->minPrice);
            })
            ->when($this->maxPrice < 100000, function ($query) {
                $query->where('price', '<=', $this->maxPrice);
            })
            ->when($this->sortBy === 'latest', function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->when($this->sortBy === 'popular', function ($query) {
                $query->withCount(['orderItems as total_sold'])
                    ->orderByDesc('total_sold')
                    ->orderByDesc('created_at');
            })
            ->when($this->sortBy === 'price_low', function ($query) {
                $query->orderBy('price', 'asc');
            })
            ->when($this->sortBy === 'price_high', function ($query) {
                $query->orderBy('price', 'desc');
            })
            ->when($this->sortBy === 'name', function ($query) {
                $query->orderBy('name', 'asc');
            })
            ->paginate($this->perPage);

        return view('livewire.shop', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
