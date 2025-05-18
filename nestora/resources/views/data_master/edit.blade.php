<!-- resources/views/data_master/edit-modal.blade.php -->

<form action="{{ route('data-master.properti.edit', $property->id) }}" method="POST" id="editPropertyForm">
    @csrf
    @method('PUT')

    <div class="grid gap-4 text-white">
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
            <select name="furnishing" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
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
            <input type="checkbox" id="verified" name="verified" value="1"
                {{ old('verified', $property->verified ?? false) ? 'checked' : '' }}>
            <label for="verified" class="text-sm text-gray-300">Verified</label>
        </div>
        <div>
            <input type="hidden" name="type" value="{{ $property->type ?? 'Residential for Sale' }}">
            <input type="hidden" name="addedOn" value="{{ $property->addedOn ?? now() }}">
        </div>
        <div class="flex justify-end space-x-3 mt-4">
            <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">
                Cancel
            </button>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                Update
            </button>
        </div>
    </div>
</form>

<script>
    document.getElementById('editPropertyForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Property updated successfully!');
                closeEditModal();
                window.location.reload();
            } else {
                let errorMessage = 'Please correct the following errors:\n';
                if (data.errors) {
                    for (const field in data.errors) {
                        errorMessage += `- ${data.errors[field].join('\n- ')}\n`;
                    }
                } else {
                    errorMessage += '- Unknown error occurred.';
                }
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the property. Please try again.');
        });
    });
</script>
