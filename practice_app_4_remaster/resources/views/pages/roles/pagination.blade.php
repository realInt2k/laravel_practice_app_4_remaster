<div class=" me-3 my-3 text-end">
    <a class="btn bg-gradient-dark mb-0 button-create" data-url="{{ route('roles.create') }}"
        data-page-number={{ $roles->currentPage() }} data-page-count-elements={{ $roles->count() }}>
        <i class="material-icons text-sm">add</i>
        &nbsp;&nbsp;Create New Role
    </a>
</div>
<div id="page-info" data-page-number={{ $roles->currentPage() }} data-page-count-elements={{ $roles->count() }} hidden>
</div>
<table class="table align-items-center mb-0">
    <thead>
        <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                ID
            </th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                NAME</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
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
                <td>
                    @foreach ($role->permissions as $index => $permission)
                        @if ($index > 1)
                            <br>
                            {{ count($role->permissions) - $index }} more...
                        @break
                    @endif
                    <div style="text-transform: none" class="badge bg-info">{{ $permission->name }}</div>
                @endforeach
            </td>
            <td class="align-middle">
                <div class="btn-group">
                    <a rel="tooltip" class="btn btn-success btn-sm btn-link button-edit"
                        data-id="{{ $role->id }}" data-url="{{ route('roles.edit', $role->id) }}">
                        <span class="material-icons" style="font-size: 150%;">edit</span>
                        <div class="ripple-container"></div>
                    </a>

                    <button type="button" class="btn btn-danger btn-sm btn-link button-delete"
                        data-id="{{ $role->id }}" data-url="{{ route('roles.destroy', $role->id) }}">
                        <span class="material-icons" style="font-size: 150%;">close</span>
                        <div class="ripple-container"></div>
                    </button>

                    <a rel="tooltip" class="btn btn-info btn-link btn-sm button-show"
                        data-id="{{ $role->id }}" data-url="{{ route('roles.show', $role->id) }}">
                        <span class="material-icons" style="font-size: 150%;">info</span>
                        <div class="ripple-container"></div>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
</table>

<div class="mt-3 mr-2 ml-2">
{{ $roles->links() }}
</div>

@if (count($roles) === 0)
<div class="alert alert-warning" role="alert">
    <strong>no records!</strong>
</div>
@endif
