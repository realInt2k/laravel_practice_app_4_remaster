@canManipulateCategory('categories-store')
<div class=" me-3 my-3 text-end">
    <a class="btn bg-gradient-dark mb-0 button-create" data-url="{{ route('categories.create') }}"><i
            class="material-icons text-sm">add</i>&nbsp;&nbsp;Create New
        Category</a>
</div>
@endcanManipulateCategory
<div id="page-info" data-page-number={{ $categories->currentPage() }} data-page-count-elements={{ $categories->count() }}
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
                PARENT CATEGORY</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                CHILDREN CATEGORIES</th>
            <th class="text-secondary opacity-7"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $category)
            <tr>
                <td>
                    <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                            <p class="mb-0 text-sm">{{ $category->id }}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $category->name }}</h6>

                    </div>
                </td>
                <td class="align-middle text-center text-sm">
                    @if ($category->parent)
                        <div style="text-transform: none" class="badge bg-primary">{{ $category->parent->name }}</div>
                    @endif
                </td>
                <td class="align-middle text-center">
                    @foreach ($category->children as $index => $cat)
                        @if ($index > 1)
                            <br>
                            {{ count($category->children) - $index }} more...
                        @break
                    @endif
                    <div style="text-transform: none" class="badge bg-info">{{ $cat->name }}</div>
                @endforeach
            </td>
            <td class="align-middle">
                <div class="btn-group">
                    @canManipulateCategory('categories-update')
                    <a rel="tooltip" class="btn btn-success btn-link btn-sm button-edit"
                        data-id="{{ $category->id }}" data-url="{{ route('categories.edit', $category->id) }}">
                        <span class="material-icons" style="font-size: 150%;">edit</span>
                        <div class="ripple-container"></div>
                    </a>
                    @endcanManipulateCategory

                    @canManipulateCategory('categories-destroy')
                    <button type="button" class="btn btn-danger btn-link btn-sm button-delete"
                        data-id="{{ $category->id }}" data-url="{{ route('categories.destroy', $category->id) }}">
                        <span class="material-icons" style="font-size: 150%;">close</span>
                        <div class="ripple-container"></div>
                    </button>
                    @endcanManipulateCategory

                    <a rel="tooltip" class="btn btn-info btn-link btn-sm btn-sm button-show"
                        data-id="{{ $category->id }}" data-url="{{ route('categories.show', $category->id) }}">
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
{{ $categories->links() }}
</div>


@if (count($categories) === 0)
<div class="alert alert-warning" role="alert">
    <strong>no records!</strong>
</div>
@endif