<div class="grid gap-4 bg-gray-900 text-white p-6 rounded-lg shadow-md">
    <div>
        <label class="block text-sm text-gray-300">Title</label>
        <input type="text" name="title" value="{{ old('title', $property->title ?? '') }}"
            class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
    </div>
    <div>
        <label class="block text-sm text-gray-300">Bedrooms</label>
        <input type="number" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms ?? '') }}"
            class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
    </div>
    <div>
        <label class="block text-sm text-gray-300">Bathrooms</label>
        <input type="number" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms ?? '') }}"
            class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
    </div>
    <div>
        <label class="block text-sm text-gray-300">Price (AED)</label>
        <input type="number" name="price" value="{{ old('price', $property->price ?? '') }}"
            class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
    </div>
    <div>
        <label class="block text-sm text-gray-300">Furnishing</label>
        <select name="furnishing"
            class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
            <option value="Yes" {{ old('furnishing', $property->furnishing ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
            <option value="No" {{ old('furnishing', $property->furnishing ?? '') == 'No' ? 'selected' : '' }}>No</option>
        </select>
    </div>
    <div>
        <label class="block text-sm text-gray-300">Address</label>
        <input type="text" name="displayAddress" value="{{ old('displayAddress', $property->displayAddress ?? '') }}"
            class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
    </div>
    <div>
        <label class="block text-sm text-gray-300">Size (sqft)</label>
        <input type="number" name="sizeMin" value="{{ old('sizeMin', $property->sizeMin ?? '') }}"
            class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
    </div>
    <div class="flex items-center space-x-2">
        <input type="checkbox" name="verified" value="1"
            {{ old('verified', $property->verified ?? false) ? 'checked' : '' }}>
        <label class="text-sm text-gray-300">Verified</label>
    </div>
    <div>
        <input type="hidden" name="type" value="Residential for Sale">
        <input type="hidden" name="addedOn" value="{{ now() }}">
    </div>
    <div>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
            {{ $button }}
        </button>
    </div>
</div>
