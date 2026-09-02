<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Category;

#[Layout('layouts.superadmin')]
class Categories extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Modal states
    public $showCreateModal = false;
    public $showConfirmDelete = false;

    // Form fields
    public $categoryId;
    public $name;
    public $description;
    public $icon;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'icon' => 'nullable|string|max:50',
        'is_active' => 'required|boolean',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->description = '';
        $this->icon = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showConfirmDelete = false;
        $this->resetForm();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function editCategory($id)
    {
        $this->resetForm();
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->icon = $category->icon;
        $this->is_active = $category->is_active;
        
        $this->showCreateModal = true;
    }

    public function saveCategory()
    {
        $this->validate();

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $category->update([
                'name' => $this->name,
                'description' => $this->description,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Kategori berhasil diperbarui');
        } else {
            Category::create([
                'name' => $this->name,
                'description' => $this->description,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Kategori berhasil ditambahkan');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->categoryId = $id;
        $this->showConfirmDelete = true;
    }

    public function deleteCategory()
    {
        $category = Category::findOrFail($this->categoryId);
        $category->delete();
        
        session()->flash('message', 'Kategori berhasil dihapus');
        $this->closeModal();
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        session()->flash('message', 'Status kategori berhasil diubah');
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount('helps')
            ->latest()
            ->paginate($this->perPage);

        return view('superadmin.categories', compact('categories'));
    }
}
