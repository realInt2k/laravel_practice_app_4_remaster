<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3>
                <small class="text-muted">category details</small>
            </h3>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="text-primary">
                        <strong>Name:</strong>
                    </div>
                    <span>
                        {{ $category->name }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="text-primary">
                    <strong>Parent:</strong>
                </div>
                @if ($category->parent)
                    <span class="badge bg-primary" style="text-transform:none">
                        {{ $category->parent->name }}
                    </span>
                @else
                    <span class="badge bg-secondary" style="text-transform:none">
                        none
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <h3>
                <small class="text-muted">category's sub-categories</small>
            </h3>

            <div class="card-body">
                @if (count($category->children) > 0)
                    @foreach ($category->children as $cat)
                        <span class="badge bg-info" style="text-transform:none">{{ $cat->name }}</span>
                    @endforeach
                @else
                    <span class="badge bg-secondary" style="text-transform:none">
                        none
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
