<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\Review;
use App\Models\PayoutSplit;
use App\StateMachines\OrderStateMachine;

class Dashboard extends Component
{
    use WithFileUploads;

    public string $activeTab = 'orders';
    public ?int $restaurantId = null;

    // Menu form
    public bool $showMenuForm = false;
    public ?int $editingItemId = null;
    public string $itemName = '';
    public string $itemDescription = '';
    public float $itemPrice = 0;
    public int $itemPrepTime = 15;
    public ?int $itemCategoryId = null;
    public $itemImage = null;
    public ?string $existingImage = null;
    public bool $removeExistingImage = false;

    // Category form
    public bool $showCategoryForm = false;
    public string $newCategoryName = '';

    // Restaurant settings
    public $restaurantLogo = null;
    public $restaurantCover = null;

    public function mount(): void
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->restaurant) {
            $this->restaurantId = $user->restaurant->id;
        } else {
            $this->restaurantId = Restaurant::first()?->id;
        }
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ── Restaurant Settings ──
    public function saveRestaurantImages(): void
    {
        $this->validate([
            'restaurantLogo' => 'nullable|image|max:2048',
            'restaurantCover' => 'nullable|image|max:4096',
        ]);

        $restaurant = Restaurant::findOrFail($this->restaurantId);

        if ($this->restaurantLogo) {
            $restaurant->logo = $this->restaurantLogo->store('restaurant-logos', 'public');
        }
        if ($this->restaurantCover) {
            $restaurant->cover = $this->restaurantCover->store('restaurant-covers', 'public');
        }

        $restaurant->save();
        $this->reset(['restaurantLogo', 'restaurantCover']);
        session()->flash('success', 'Restaurant images updated!');
    }

    public function removeRestaurantLogo(): void
    {
        $restaurant = Restaurant::findOrFail($this->restaurantId);
        $restaurant->update(['logo' => null]);
        session()->flash('success', 'Logo removed.');
    }

    public function removeRestaurantCover(): void
    {
        $restaurant = Restaurant::findOrFail($this->restaurantId);
        $restaurant->update(['cover' => null]);
        session()->flash('success', 'Cover removed.');
    }

    // ── Order Actions ──
    public function confirmOrder(int $orderId): void
    {
        $this->transitionOrder($orderId, 'confirmed');
    }

    public function startPreparing(int $orderId): void
    {
        $this->transitionOrder($orderId, 'preparing');
    }

    public function cancelOrder(int $orderId): void
    {
        $this->transitionOrder($orderId, 'cancelled');
    }

    public array $selectedRiders = [];

    private function transitionOrder(int $orderId, string $newStatus): void
    {
        $order = Order::findOrFail($orderId);
        try {
            $sm = new OrderStateMachine($order);
            $sm->transitionTo($newStatus, 'restaurant', $this->restaurantId);
            session()->flash('success', "Order #{$orderId} → " . ucfirst($newStatus));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function assignRider(int $orderId): void
    {
        if (empty($this->selectedRiders[$orderId])) {
            session()->flash('error', 'Please select a rider first.');
            return;
        }

        $order = Order::where('restaurant_id', $this->restaurantId)->findOrFail($orderId);
        $order->rider_id = $this->selectedRiders[$orderId];
        $order->save();

        session()->flash('success', "Rider assigned to order #{$orderId} successfully.");
    }

    // ── Category Actions ──
    public function createCategory(): void
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255',
        ]);

        MenuCategory::create([
            'restaurant_id' => $this->restaurantId,
            'name' => $this->newCategoryName,
            'sort_order' => MenuCategory::where('restaurant_id', $this->restaurantId)->count(),
        ]);

        $this->newCategoryName = '';
        $this->showCategoryForm = false;
        session()->flash('success', 'Category created!');
    }

    public function deleteCategory(int $categoryId): void
    {
        $category = MenuCategory::where('restaurant_id', $this->restaurantId)->findOrFail($categoryId);
        $category->delete();
        session()->flash('success', 'Category deleted.');
    }

    // ── Menu Actions ──
    public function toggleAvailability(int $itemId): void
    {
        $item = MenuItem::findOrFail($itemId);
        $item->is_available = !$item->is_available;
        $item->save();
    }

    public function editItem(int $itemId): void
    {
        $item = MenuItem::findOrFail($itemId);
        $this->editingItemId = $itemId;
        $this->itemName = $item->name;
        $this->itemDescription = $item->description ?? '';
        $this->itemPrice = $item->base_price;
        $this->itemPrepTime = $item->preparation_time ?? 15;
        $this->itemCategoryId = $item->menu_category_id;
        $this->existingImage = $item->image;
        $this->itemImage = null;
        $this->removeExistingImage = false;
        $this->showMenuForm = true;
    }

    public function newItem(): void
    {
        $this->reset(['editingItemId', 'itemName', 'itemDescription', 'itemPrice', 'itemPrepTime', 'itemImage', 'existingImage', 'removeExistingImage']);
        $restaurant = Restaurant::find($this->restaurantId);
        $this->itemCategoryId = $restaurant?->menuCategories()->first()?->id;
        $this->showMenuForm = true;
    }

    public function removeImage(): void
    {
        $this->removeExistingImage = true;
        $this->existingImage = null;
        $this->itemImage = null;
    }

    public function saveItem(): void
    {
        $this->validate([
            'itemName' => 'required|string|max:255',
            'itemPrice' => 'required|numeric|min:0',
            'itemCategoryId' => 'required|exists:menu_categories,id',
            'itemImage' => 'nullable|image|max:2048',
        ], [
            'itemCategoryId.required' => 'Please select a category. Create one first if none exist.',
            'itemImage.image' => 'The file must be an image (jpg, png, gif, etc.)',
            'itemImage.max' => 'Image must be less than 2MB.',
        ]);

        $data = [
            'menu_category_id' => $this->itemCategoryId,
            'name' => $this->itemName,
            'description' => $this->itemDescription,
            'base_price' => $this->itemPrice,
            'preparation_time' => $this->itemPrepTime,
            'is_available' => true,
        ];

        // Handle image upload
        if ($this->itemImage) {
            $data['image'] = $this->itemImage->store('menu-items', 'public');
        } elseif ($this->removeExistingImage) {
            $data['image'] = null;
        }

        MenuItem::updateOrCreate(['id' => $this->editingItemId], $data);

        $this->showMenuForm = false;
        $this->reset(['itemImage', 'existingImage', 'removeExistingImage']);
        session()->flash('success', $this->editingItemId ? 'Item updated!' : 'Item created!');
    }

    public function deleteItem(int $itemId): void
    {
        MenuItem::destroy($itemId);
        session()->flash('success', 'Item deleted.');
    }

    public function render()
    {
        $restaurant = Restaurant::with('menuCategories.menuItems.variants')->find($this->restaurantId);

        $orders = Order::with(['customer', 'items.menuItem', 'rider'])
            ->where('restaurant_id', $this->restaurantId)
            ->latest()->get();

        $activeOrders = $orders->whereNotIn('status', ['delivered', 'cancelled']);
        $deliveredOrders = $orders->where('status', 'delivered');

        $totalRevenue = PayoutSplit::whereHas('order', fn($q) => $q->where('restaurant_id', $this->restaurantId))->sum('restaurant_amount');
        $pendingPayouts = PayoutSplit::where('is_paid', false)->whereHas('order', fn($q) => $q->where('restaurant_id', $this->restaurantId))->sum('restaurant_amount');

        $reviews = Review::where('reviewable_type', Restaurant::class)
            ->where('reviewable_id', $this->restaurantId)
            ->with('user')->latest()->get();

        $avgRating = $reviews->avg('rating');

        $payouts = PayoutSplit::whereHas('order', fn($q) => $q->where('restaurant_id', $this->restaurantId))
            ->with('order')->latest()->limit(20)->get();

        $onlineRiders = \App\Models\Rider::with('user')->where('is_online', true)->get();

        return view('livewire.restaurant.dashboard', compact(
            'restaurant', 'orders', 'activeOrders', 'deliveredOrders',
            'totalRevenue', 'pendingPayouts', 'reviews', 'avgRating', 'payouts', 'onlineRiders'
        ))->layout('components.layouts.app', ['title' => ($restaurant->name ?? 'Restaurant') . ' Dashboard — DeliverEats']);
    }
}
