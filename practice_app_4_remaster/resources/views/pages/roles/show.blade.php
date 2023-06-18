<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3>
                <small class="text-muted">Role details</small>
            </h3>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="text-primary">
                        <strong>Name:</strong>
                    </div>
                    <span>
                        {{ $role->name }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h3>
                <small class="text-muted">Role's Permissions</small>
            </h3>

            <div class="card-body">
                @if (count($role->permissions) > 0)
                    @foreach ($role->permissions as $permission)
                        <span class="badge bg-info">{{ $permission->name }}</span>
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
