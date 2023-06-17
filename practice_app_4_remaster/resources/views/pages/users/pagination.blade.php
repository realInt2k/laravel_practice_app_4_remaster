<table class="table align-items-center mb-0">
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
                    @foreach ($user->roles as $role)
                        <span class="text-secondary text-xs font-weight-bold">{{ $role->name }}</span>
                    @endforeach
                </td>
                <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">22/03/18</span>
                </td>
                <td class="align-middle">
                    <a rel="tooltip" class="btn btn-success btn-link button-edit" data-id="{{ $user->id }}"
                        data-page-number={{ $users->currentPage() }} data-url="{{ route('users.edit', $user->id) }}">
                        <i class="material-icons">edit</i>
                        <div class="ripple-container"></div>
                    </a>

                    <button type="button" class="btn btn-danger btn-link button-create"
                        data-page-number={{ $users->currentPage() }} data-id="{{ $user->id }}"
                        data-url="{{ route('users.destroy', $user->id) }}">
                        <i class="material-icons">close</i>
                        <div class="ripple-container"></div>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3 mr-2 ml-2">
    {{ $users->links() }}
</div>
