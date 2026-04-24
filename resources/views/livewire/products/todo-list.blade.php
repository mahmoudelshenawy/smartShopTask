<div class="max-w-lg mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-6">🛒 Item List (Livewire CRUD)- {{ auth()->user()->name }}</h2>

    {{-- Add Form --}}
    <div class="flex gap-2 mb-6">
        <input wire:model="name" wire:keydown.enter="add" type="text" placeholder="Add new item..."
            class="flex-1 border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        <button wire:click="add" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Add
        </button>
    </div>

    @error('name')
        <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
    @enderror

    {{-- List --}}
    <ul class="space-y-2">
        @forelse($items as $item)
            <li class="flex items-center justify-between bg-white rounded shadow px-4 py-3">

                @if ($editingId === $item['id'])
                    {{-- Edit mode --}}
                    <input wire:model="editingName" wire:keydown.enter="saveEdit"
                        wire:keydown.escape="$set('editingId', null)" type="text"
                        class="flex-1 border rounded px-2 py-1 mr-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
                    <div class="flex gap-2">
                        <button wire:click="saveEdit"
                            class="text-green-600 font-semibold hover:underline text-sm">Save</button>
                        <button wire:click="$set('editingId', null)"
                            class="text-gray-400 hover:underline text-sm">Cancel</button>
                    </div>
                @else
                    {{-- View mode --}}
                    <span class="text-gray-800">{{ $item['name'] }}</span>
                    <div class="flex gap-3">
                        <button wire:click="startEdit({{ $item['id'] }})"
                            class="text-blue-500 text-sm hover:underline">Edit</button>
                        <button wire:click="delete({{ $item['id'] }})"
                            class="text-red-500 text-sm hover:underline">Delete</button>
                    </div>
                @endif

            </li>
        @empty
            <li class="text-gray-400 text-center py-6">No items yet. Add one above.</li>
        @endforelse
    </ul>
</div>
