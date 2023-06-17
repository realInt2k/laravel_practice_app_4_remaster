<table class="table align-items-center mb-0">
    <thead>
        <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                ID
            </th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                NAME</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                PERMISSIONS</th>
            <th class="text-secondary opacity-7"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($roles as $role)
            <tr>
                <td>
                    <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                            <p class="mb-0 text-sm">{{ $role->id }}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $role->name }}</h6>

                    </div>
                </td>
                <td class="align-middle text-center text-sm">
                    {{ 'PERMISSIONS' }}

                </td>
                <td class="align-middle">
                    <a rel="tooltip" class="btn btn-success btn-link button-edit" data-id="{{ $role->id }}"
                        data-page-number={{ $roles->currentPage() }} data-url="{{ route('roles.edit', $role->id) }}">
                        <i class="material-icons">edit</i>
                        <div class="ripple-container"></div>
                    </a>

                    <button type="button" class="btn btn-danger btn-link button-create"
                        data-page-number={{ $roles->currentPage() }} data-id="{{ $role->id }}"
                        data-url="{{ route('roles.destroy', $role->id) }}">
                        <i class="material-icons">close</i>
                        <div class="ripple-container"></div>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3 mr-2 ml-2">
    {{ $roles->links() }}
</div>
