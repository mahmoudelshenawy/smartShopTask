<?php

namespace App\Livewire\Products;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.client')]
#[Title('Todo List')]
class TodoList extends Component
{
    public string $name = '';

    public ?int $editingId = null;

    public string $editingName = '';

    public array $items = [
        ['id' => 1, 'name' => 'Apple'],
        ['id' => 2, 'name' => 'Banana'],
        ['id' => 3, 'name' => 'Cherry'],
    ];

    public function add(): void
    {
        $this->validate(['name' => 'required|min:2']);

        $this->items[] = [
            'id' => time(),
            'name' => trim($this->name),
        ];

        $this->reset('name');
    }

    public function startEdit(int $id): void
    {
        $this->editingId = $id;
        $item = collect($this->items)->firstWhere('id', $id);
        $this->editingName = $item['name'];
    }

    public function saveEdit(): void
    {
        $this->validate(['editingName' => 'required|min:2']);

        $this->items = collect($this->items)->map(function ($item) {
            if ($item['id'] === $this->editingId) {
                $item['name'] = trim($this->editingName);
            }

            return $item;
        })->toArray();

        $this->reset('editingId', 'editingName');
    }

    public function delete(int $id): void
    {
        $this->items = collect($this->items)
            ->reject(fn ($item) => $item['id'] === $id)
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.products.todo-list');
    }
}
