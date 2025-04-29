@extends('layout.apps')

@section('konten')
<div class="container">
    <h2>Predict Listing Outcome</h2>

    <form method="POST" action="{{ url('/predict') }}">
        @csrf

        <div class="mb-3">
            <label for="bathrooms" class="form-label">Bathrooms</label>
            <input
                type="number"
                step="0.1"
                class="form-control"
                id="bathrooms"
                name="bathrooms"
                required
            >
        </div>

        <div class="mb-3">
            <label for="bedrooms" class="form-label">Bedrooms</label>
            <input
                type="number"
                step="0.1"
                class="form-control"
                id="bedrooms"
                name="bedrooms"
                required
            >
        </div>

        <div class="mb-3">
            <label for="furnishing" class="form-label">Furnishing (0 = unfurnished, 1 = furnished)</label>
            <input
                type="number"
                class="form-control"
                id="furnishing"
                name="furnishing"
                min="0"
                max="1"
                required
            >
        </div>

        <div class="mb-3">
            <label for="sizeMin" class="form-label">Size Min (m²)</label>
            <input
                type="number"
                class="form-control"
                id="sizeMin"
                name="sizeMin"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary">Predict</button>
    </form>

    @if(session('prediction_result'))
        <div class="alert alert-success mt-3">
            Hasil Prediksi: <strong>{{ session('prediction_result') }}</strong>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mt-3">
            Terjadi kesalahan: <strong>{{ session('error') }}</strong>
        </div>
    @endif
</div>
@endsection
