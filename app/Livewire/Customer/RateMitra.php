<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Help;
use App\Models\Rating;

class RateMitra extends Component
{
    public $help;
    public $rating = 0;
    public $review = '';
    public $is_anonymous = false;
    public $alreadyRated = false;

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'nullable|string|max:500',
        'is_anonymous' => 'boolean',
    ];

    public function mount($helpId)
    {
        $this->help = Help::with('mitra')->findOrFail($helpId);

        $this->alreadyRated = Rating::hasRated(
            $this->help->id,
            auth()->id(),
            'customer_to_mitra'
        );
    }

    public function setRating($value)
    {
        $this->rating = (int) $value;
    }

    public function submitRating()
    {
        $this->validate();

        if (!in_array($this->help->status, ['completed', 'selesai'])) {
            $this->dispatch('notify', type: 'error', message: 'Hanya bisa memberi rating untuk bantuan yang sudah selesai.');
            return;
        }

        if ($this->alreadyRated) {
            $this->dispatch('notify', type: 'error', message: 'Anda sudah memberikan rating untuk mitra ini.');
            return;
        }

        if ($this->help->user_id !== auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak berhak memberikan rating untuk bantuan ini.');
            return;
        }

        Rating::create([
            'help_id' => $this->help->id,
            'rater_id' => auth()->id(),
            'ratee_id' => $this->help->mitra_id,
            'type' => 'customer_to_mitra',
            'rating' => $this->rating,
            'review' => $this->review,
            'is_anonymous' => $this->is_anonymous,
            // Legacy fields
            'user_id' => auth()->id(),
            'mitra_id' => $this->help->mitra_id,
        ]);

        $this->alreadyRated = true;
        $this->dispatch('notify', type: 'success', message: 'Terima kasih, rating berhasil dikirim');

        // notify browser so parent can close modal
        $this->dispatch('rating-submitted', helpId: $this->help->id);
        
        // notify parent components if needed
        $this->dispatch('ratingSubmitted');
    }

    public function resetForm()
    {
        $this->reset(['rating', 'review', 'is_anonymous']);
    }

    public function render()
    {
        return view('livewire.customer.rate-mitra');
    }
}
