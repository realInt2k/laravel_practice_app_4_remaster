@canManipulateUser('users.store')
<div class=" me-3 my-3 text-end">
    <a class="btn bg-gradient-dark mb-0 button-create" data-url="{{ route('users.create') }}"
        data-page-number={{ $users->currentPage() }} data-page-count-elements={{ $users->count() }}>
        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New
        User</a>
</div>
@endcanManipulateUser
<div id="page-info" data-page-number={{ $users->currentPage() }} data-page-count-elements={{ $users->count() }} hidden>
</div>
<table class="table align-items-center mb-0 table-hover">
    <thead>
        <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                ID
            </th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                NAME</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                EMAIL</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                ROLE</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                CREATION DATE
            </th>
            <th class="text-secondary opacity-7"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td>
                    <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                            <p class="mb-0 text-sm">{{ $user->id }}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $user->name }}</h6>

                    </div>
                </td>
                <td class="align-middle text-center text-sm">
                    <p class="text-xs text-secondary mb-0">{{ $user->email }}
                    </p>
                </td>
                <td class="align-middle text-center">
                    @foreach ($user->roles as $index => $role)
                        @if ($index > 1)
                            <br>
                            {{ count($user->roles) - $index }} more...
                            @break
                        @endif
                        <span class="badge bg-primary" style="text-transform: none">{{ $role->name }}</span>
                    @endforeach
                </td>
                <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">22/03/18</span>
                </td>
                <td class="align-middle">
                    <div class="btn-group">
                        @canManipulateUser('users.update', $user)
                        <a rel="tooltip" class="btn btn-success btn-link btn-sm button-edit"
                            data-id="{{ $user->id }}"
                            data-url="{{ route('users.edit', $user->id) }}">
                            <span class="material-icons" style="font-size: 150%;">edit</span>
                            <div class="ripple-container"></div>
                        </a>
                        @endcanManipulateUser

                        @canManipulateUser('users.destroy', $user)
                        <button type="button" class="btn btn-danger btn-link btn-sm button-delete"
                            data-id="{{ $user->id }}"
                            data-url="{{ route('users.destroy', $user->id) }}">
                            <span class="material-icons" style="font-size: 150%;">close</span>
                            <div class="ripple-container"></div>
                        </button>
                        @endcanManipulateUser

                        <a rel="tooltip" class="btn btn-info btn-link btn-sm button-show"
                            data-id="{{ $user->id }}"
                            data-url="{{ route('users.show', $user->id) }}">
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
{{ $users->links() }}
</div>

@if (count($users) === 0)
<div class="alert alert-warning" role="alert">
    <strong>no records!</strong>
</div>
@endif
