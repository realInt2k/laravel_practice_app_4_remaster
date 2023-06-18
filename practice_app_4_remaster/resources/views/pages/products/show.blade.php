<div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-5">
        <h3>
            <small class="text-muted">Product details</small>
        </h3>
        <div class="card-body">
            <div class="row mb-3">
                <div class="text-primary">
                    <strong>Name:</strong>
                </div>
                <div>
                    {{ $product->name }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="text-primary">
                    <strong>Description:</strong>
                </div>
                <div>
                    {{ $product->description }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-5">
        <h3>
            <small class="text-muted">Product category</small>
        </h3>

        <div>
            @if (count($product->categories) > 0)
                @foreach ($product->categories as $category)
                    <div class="badge bg-info" style="text-transform: none">{{ $category->name }}</div>
                @endforeach
            @else
                <div class="badge bg-secondary" style="text-transform: none"> no category </div>
            @endif
        </div>

        <h3>
            <small class="text-muted">product visual 🖼️</small>
        </h3>
        <img id="preview-image" class="img-fluid rounded mb-2"
            src="{{ $product->image_path ? asset($product->image_path) : '#' }}" alt="Image preview"
            {{ $product->image_path ? '' : 'hidden' }}>
    </div>
    <div class="col-sm-1"></div>
</div>
