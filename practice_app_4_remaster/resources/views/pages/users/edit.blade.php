<form id="form-data" action="{{ route('users.update', $user->id) }}" method="post" data-method="put">
    @csrf
    @method('put')
    <div class="row">
        <div class="col-sm-1"></div>
        @hasRole('super-admin')
            <div class="col-sm-5">
        @else
            <div class="col-sm-10">
        @endhasRole
            <h3>
                <small class="text-muted">User details</small>
            </h3>
            <div>
                Name
            </div>
            <span style="font-size:0.75rem" id="error-name" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <span class="input-group-text text-danger mr-4">*</span>
                <input type="text" class="form-control" id="input-name" name="name" placeholder="name"
                    value="{{ $user->name }}">
            </div>

            <div>
                Email
            </div>
            <span style="font-size:0.75rem" id="error-email" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <span class="input-group-text text-danger">*</span>
                <input type="text" class="form-control" id="input-email" name="email"
                    placeholder="something@deha-soft.com" value="{{ $user->email }}">
            </div>

            <div>
                Update Password
            </div>
            <span style="font-size:0.75rem" id="error-password" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <input type="text" name="password" class="form-control" id="input-password"
                    placeholder="update password">
            </div>
        </div>
        @hasRole('super-admin')
        <div class="col-sm-5">
            <h3>
                <small class="text-muted">roles</small>
            </h3>
                <select style="width: 100%" class="select2" multiple="multiple" name="roles[]">
                    @foreach ($roles as $role)
                        <option {{ $user->existsRoleId($role->id) ? 'selected' : '' }} value="{{ $role->id }}">
                            {{ $role->name }}</option>
                    @endforeach
                </select>
            <h3>
                <small class="text-muted">permissions</small>
            </h3>
                <select style="width: 100%" class="form-control select2" multiple="multiple" name="permissions[]">
                    @foreach ($permissions as $permission)
                        <option {{ $user->existsPermissionId($permission->id) ? 'selected' : '' }}
                            value="{{ $permission->id }}">
                            {{ $permission->name }}</option>
                    @endforeach
                </select>
        </div>
        @endhasRole
        <div class="col-sm-1"></div>
    </div>
</form>

<script>
    $(function(yourcode) {
        yourcode(window.jQuery, window, document);
    }(function($, window, document) {
        $(function() {
            $(".select2").select2();
        })
    }))
</script>
