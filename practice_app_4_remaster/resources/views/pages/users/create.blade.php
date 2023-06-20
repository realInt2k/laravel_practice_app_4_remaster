<form id="form-data" action="{{ route('users.store') }}" method="post" data-method="post">
    @csrf
    <div class="row">
        <div class="col-sm-1"></div>
        @hasRole('super-admin')
            <div class="col-sm-5">
        @else
            <div class="col-sm-10">
        @endhasRole
            <h3>
                <small class="text-muted">User Details</small>
            </h3>
            <div>
                Name
            </div>
            <span style="font-size:0.75rem" id="error-name" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <span class="input-group-text text-danger mr-4">*</span>
                <input type="text" class="form-control" id="input-name" name="name" placeholder="name">
            </div>

            <div>
                Email
            </div>
            <span style="font-size:0.75rem" id="error-email" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <span class="input-group-text text-danger">*</span>
                <input type="text" class="form-control" id="input-email" name="email"
                    placeholder="something@deha-soft.com" >
            </div>

            <div>
                Password
            </div>
            <span style="font-size:0.75rem" id="error-password" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <span class="input-group-text text-danger mr-4">*</span>
                <input type="text" name="password" class="form-control" id="input-password"
                    placeholder="select password">
            </div>
        </div>
        @hasRole('super-admin')
        <div class="col-sm-5">
            <h3>
                <small class="text-muted">roles</small>
            </h3>
            <select style="width: 100%" class="select2" multiple="multiple" name="roles[]">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">
                        {{ $role->name }}</option>
                @endforeach
            </select>

            <h3>
                <small class="text-muted">permissions</small>
            </h3>
            <select style="width: 100%" class="form-control select2" multiple="multiple" name="permissions[]">
                @foreach ($permissions as $permission)
                    <option value="{{ $permission->id }}">
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
