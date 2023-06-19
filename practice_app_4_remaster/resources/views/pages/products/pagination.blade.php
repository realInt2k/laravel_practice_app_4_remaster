@canManipulateProduct('products-store')
<div class=" me-3 my-3 text-end">
    <a class="btn bg-gradient-dark mb-0 button-create" data-url="{{ route('products.create') }}"
        data-page-number={{ $products->currentPage() }} data-page-count-elements={{ $products->count() }}><i
            class="material-icons text-sm">add</i>&nbsp;&nbsp;Create New
        Product</a>
</div>
@endcanManipulateProduct
<div id="page-info" data-page-number={{ $products->currentPage() }} data-page-count-elements={{ $products->count() }}
    hidden>
</div>
<table class="table align-items-center mb-0">
    <thead>
        <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                ID
            </th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                NAME</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                CATEGORY</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                DESCRIPTION</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                VISUAL
            </th>
            <th class="text-secondary opacity-7"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td>
                    <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                            <p class="mb-0 text-sm">{{ $product->id }}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $product->name }}</h6>

                    </div>
                </td>
                <td class="align-middle text-center text-sm">
                    @foreach ($product->categories as $index => $category)
                        @if ($index > 1)
                            <br>
                            {{ count($product->categories) - $index }} more...
                        @break
                    @endif
                    <div class="badge bg-info" style="text-transform: none">{{ $category->name }}</div>
                @endforeach
            </td>
            <td class="align-middle text-center">
                @if (strlen($product->description) > 30)
                    <p class="text-xs text-secondary mb-0">{{ substr($product->description, 0, 30) . '...' }}
                    </p>
                @else
                    <p class="text-xs text-secondary mb-0">{{ $product->description }}
                    </p>
                @endif
            </td>
            <td class="align-middle text-center">
                <img id="preview" src="{{ $product->image_path ? asset($product->image_path) : '#' }}"
                    alt="product image" class="rounded mb-2" {{ $product->image_path ? '' : 'hidden' }}
                    height="100px" />
            </td>
            <td class="align-middle">
                <div class="btn-group">
                    @canManipulateProduct('products-update', $product)
                    <a rel="tooltip" class="btn btn-success btn-link btn-sm button-edit"
                        data-id="{{ $product->id }}" data-page-number={{ $products->currentPage() }}
                        data-page-count-elements={{ $products->count() }}
                        data-url="{{ route('products.edit', $product->id) }}">
                        <span class="material-icons" style="font-size: 150%;">edit</span>
                        <div class="ripple-container"></div>
                    </a>
                    @endcanManipulateProduct
                    @canManipulateProduct('products-destroy', $product)
                    <button type="button" class="btn btn-danger btn-sm btn-link button-delete"
                        data-page-number={{ $products->currentPage() }} data-id="{{ $product->id }}"
                        data-page-count-elements={{ $products->count() }}
                        data-url="{{ route('products.destroy', $product->id) }}">
                        <span class="material-icons" style="font-size: 150%;">close</span>
                        <div class="ripple-container"></div>
                    </button>
                    @endcanManipulateProduct
                    <a rel="tooltip" class="btn btn-info btn-link btn-sm button-show"
                        data-id="{{ $product->id }}" data-page-number={{ $products->currentPage() }}
                        data-page-count-elements={{ $products->count() }}
                        data-url="{{ route('products.show', $product->id) }}">
                        <span class="material-icons" style="font-size: 150%;">search</span>
                        <div class="ripple-container"></div>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
</table>

<div class="mt-3 mr-2 ml-2">
{{ $products->links() }}
</div>

@if (count($products) === 0)
<div class="alert alert-warning" role="alert">
    <strong>no records!</strong>
</div>
@endif
