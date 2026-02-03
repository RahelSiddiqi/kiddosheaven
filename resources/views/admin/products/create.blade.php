@extends('admin.layout')

@section('title', 'Create Product — Admin')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>Create Product</h2>
            <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn-secondary">Back to Products</a>
        </div>

        <form action="{{ route('admin.products.store') }}" method="post">
            @csrf

            <div class="admin-form-grid">
                <div class="admin-field" style="grid-column:1/-1;">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="admin-field">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select category</option>
                        <option value="Stuffed Animals" {{ old('category') === 'Stuffed Animals' ? 'selected' : '' }}>Stuffed Animals</option>
                        <option value="Wooden Toys" {{ old('category') === 'Wooden Toys' ? 'selected' : '' }}>Wooden Toys</option>
                        <option value="Educational Toys" {{ old('category') === 'Educational Toys' ? 'selected' : '' }}>Educational Toys</option>
                        <option value="Action Figures" {{ old('category') === 'Action Figures' ? 'selected' : '' }}>Action Figures</option>
                    </select>
                </div>

                <div class="admin-field">
                    <label for="price">Price (in cents) *</label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" min="1" required>
                    <small style="color:#6b7280;font-size:12px;">e.g., 3000 = $30.00</small>
                </div>

                <div class="admin-field" style="grid-column:1/-1;">
                    <label for="image_path">Image Path</label>
                    <input type="text" id="image_path" name="image_path" value="{{ old('image_path') }}" placeholder="/storage/products/1.jpeg">
                    <small style="color:#6b7280;font-size:12px;">Path relative to public directory</small>
                </div>

                <div class="admin-field" style="grid-column:1/-1;">
                    <label for="short_description">Short Description</label>
                    <input type="text" id="short_description" name="short_description" value="{{ old('short_description') }}" maxlength="500">
                </div>

                <div class="admin-field" style="grid-column:1/-1;">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="admin-field">
                    <label>
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        Featured Product
                    </label>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:12px;">
                <button type="submit" class="admin-btn admin-btn-primary">Create Product</button>
                <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
