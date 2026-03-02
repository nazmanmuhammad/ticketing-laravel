<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;

#[Layout('layouts.master')]
class CategorySettings extends Component
{
    public string $name = '';
    public ?int $parent_id = null;
    public ?int $editingId = null;
    public bool $showForm = false;

    public function create(): void
    {
        $this->reset(['name', 'parent_id', 'editingId']);
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editingId = $cat->id;
        $this->name = $cat->name;
        $this->parent_id = $cat->parent_id;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($this->editingId) {
            $cat = Category::findOrFail($this->editingId);
            $cat->update(['name' => $this->name, 'parent_id' => $this->parent_id]);
            $this->dispatch('toast', type: 'success', message: 'Category updated');
        } else {
            Category::create(['name' => $this->name, 'parent_id' => $this->parent_id]);
            $this->dispatch('toast', type: 'success', message: 'Category created');
        }

        $this->reset(['name', 'parent_id', 'editingId', 'showForm']);
    }

    public function delete(int $id): void
    {
        Category::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Category deleted');
    }

    public function render()
    {
        $categories = Category::parents()->with('children')->get();
        return view('livewire.settings.category-settings', compact('categories'));
    }
}
