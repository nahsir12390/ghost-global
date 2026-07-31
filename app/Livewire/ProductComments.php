<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Product;

class ProductComments extends Component
{
    use WithPagination;

    public Product $product;
    public $rating = 5;
    public $comment = '';
    public $guest_name = '';
    public $guest_email = '';
    public $isSubmitting = false;

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:5|max:1000',
        'guest_name' => 'nullable|string|max:255',
        'guest_email' => 'nullable|email',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function submitComment()
    {
        // Validation
        $this->validate();

        // Validate guest fields if not authenticated
        if (!Auth::check()) {
            if (empty($this->guest_name) || empty($this->guest_email)) {
                $this->addError('guest_info', 'Please provide your name and email.');
                return;
            }
        }

        $this->isSubmitting = true;

        try {
            Comment::create([
                'product_id' => $this->product->id,
                'user_id' => Auth::id(),
                'guest_name' => Auth::check() ? null : $this->guest_name,
                'guest_email' => Auth::check() ? null : $this->guest_email,
                'rating' => $this->rating,
                'comment' => $this->comment,
                'is_approved' => true, // Auto-approve
            ]);

            // Reset form
            $this->reset(['rating', 'comment', 'guest_name', 'guest_email']);
            $this->resetPage();
            
            $this->dispatch('notify', message: 'Thank you! Your comment has been posted.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Error posting comment. Please try again.', type: 'error');
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function getAuthorName()
    {
        return Auth::check() ? Auth::user()->name : $this->guest_name;
    }

    public function render()
    {
        $comments = $this->product->comments()->paginate(5);
        $averageRating = $this->product->comments()->avg('rating') ?? 0;
        $totalComments = $this->product->comments()->count();

        return view('livewire.product-comments', [
            'comments' => $comments,
            'averageRating' => $averageRating,
            'totalComments' => $totalComments,
        ]);
    }
}
