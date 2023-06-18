<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3>
                <small class="text-muted">User details</small>
            </h3>
            <div class="card-body">
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label text-primary">{{ __('Name') }}</label>
                    <span>
                        {{ $user->name }}
                    </span>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label text-primary">{{ __('Email') }}</label>
                    <div class="col-sm-9">
                        {{ $user->email }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h3>
                <small class="text-muted">Roles</small>
            </h3>

            <div class="card-body">
                @if(count($user->roles) > 0)
                    @foreach ($user->roles as $role)
                        <span class="badge bg-primary">{{ $role->name }}</span>
                    @endforeach
                @else
                    <span class="badge bg-secondary" style="text-transform:none">
                        none
                    </span>
                @endif
            </div>
            <h3>
                <small class="text-muted">Permissions</small>
            </h3>
            <div class="card-body">
                @if(count($user->permissions) > 0)
                    @foreach ($user->permissions as $permission)
                        <span class="badge bg-primary">{{ $permission->name }}</span>
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
