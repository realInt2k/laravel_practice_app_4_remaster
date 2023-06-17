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
                    {{ 'category' }}

                </td>
                <td class="align-middle text-center">
                    <p class="text-xs text-secondary mb-0">{{ $product->email }}
                    </p>
                </td>
                <td class="align-middle text-center">
                    {{ 'VISUAL' }}
                </td>
                <td class="align-middle">
                    <a rel="tooltip" class="btn btn-success btn-link button-edit" data-id="{{ $product->id }}"
                        data-page-number={{ $products->currentPage() }}
                        data-page-count-elements={{ $products->count() }}
                        data-url="{{ route('products.edit', $product->id) }}">
                        <i class="material-icons">edit</i>
                        <div class="ripple-container"></div>
                    </a>

                    <button type="button" class="btn btn-danger btn-link button-delete"
                        data-page-number={{ $products->currentPage() }} data-id="{{ $product->id }}"
                        data-page-count-elements={{ $products->count() }}
                        data-url="{{ route('products.destroy', $product->id) }}">
                        <i class="material-icons">close</i>
                        <div class="ripple-container"></div>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3 mr-2 ml-2">
    {{ $products->links() }}
</div>
