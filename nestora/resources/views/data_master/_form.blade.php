<div class="grid gap-4">
    <div>
        <label class="block text-sm">Judul</label>
        <input type="text" name="title" value="{{ old('title', $property->title ?? '') }}" class="w-full border rounded p-2">
    </div>
    <div>
        <label class="block text-sm">Kamar Tidur</label>
        <input type="number" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms ?? '') }}" class="w-full border rounded p-2">
    </div>
    <div>
        <label class="block text-sm">Kamar Mandi</label>
        <input type="number" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms ?? '') }}" class="w-full border rounded p-2">
    </div>
    <div>
        <label class="block text-sm">Harga (AED)</label>
        <input type="number" name="price" value="{{ old('price', $property->price ?? '') }}" class="w-full border rounded p-2">
    </div>
    <div>
        <label class="block text-sm">Tipe</label>
        <input type="text" name="type" value="{{ old('type', $property->type ?? '') }}" class="w-full border rounded p-2">
    </div>
    <div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
            {{ $button }}
        </button>
    </div>
</div>
