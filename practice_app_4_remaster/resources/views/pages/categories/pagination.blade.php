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
                    {{ 'PARENT' }}

                </td>
                <td class="align-middle text-center">
                    {{ 'CHILD' }}
                </td>
                <td class="align-middle">
                    <a rel="tooltip" class="btn btn-success btn-link button-edit" data-id="{{ $category->id }}"
                        data-page-number={{ $categories->currentPage() }}
                        data-url="{{ route('categories.edit', $category->id) }}">
                        <i class="material-icons">edit</i>
                        <div class="ripple-container"></div>
                    </a>

                    <button type="button" class="btn btn-danger btn-link button-create"
                        data-page-number={{ $categories->currentPage() }} data-id="{{ $category->id }}"
                        data-url="{{ route('categories.destroy', $category->id) }}">
                        <i class="material-icons">close</i>
                        <div class="ripple-container"></div>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3 mr-2 ml-2">
    {{ $categories->links() }}
</div>
