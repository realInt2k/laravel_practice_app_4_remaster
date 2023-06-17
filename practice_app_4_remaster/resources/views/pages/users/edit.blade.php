<form id="form-data" action="{{ route('users.update', $user->id) }}" method="post" data-method="put">
    @csrf
    @method('put')
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-5">
            <h3>
                User
                <small class="text-muted">details</small>
            </h3>
            <span style="font-size:0.75rem" id="error-name" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <span class="input-group-text text-danger mr-4">*</span>
                <input type="text" class="form-control" id="input-name" name="name" placeholder="name"
                    value="{{ $user->name }}">
            </div>

            <span style="font-size:0.75rem" id="error-email" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <span class="input-group-text text-danger">*</span>
                <input type="text" class="form-control" id="input-email" name="email"
                    placeholder="something@deha-soft.com" value="{{ $user->email }}">
            </div>

            <span style="font-size:0.75rem" id="error-password" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <input type="text" name="password" class="form-control" id="input-password"
                    placeholder="update password">
            </div>
        </div>
        <div class="col-sm-5">
            <h3>
                <small class="text-muted">roles</small>
            </h3>
            <select style="width: 100%" class="select2" multiple="multiple" name="roles[]">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>

            <h3>
                <small class="text-muted">permissions</small>
            </h3>
            <select style="width: 100%" class="form-control select2" multiple="multiple" name="permissions[]">
                @foreach ($permissions as $permission)
                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                @endforeach
            </select>
        </div>
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
