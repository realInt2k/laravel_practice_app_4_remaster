<form id="form-data" action="{{ route('roles.store') }}" method="post" data-method="post">
    @csrf
    <div class="row">
        <div class="col-sm-1"></div>
        @allowedToChangeRoleAndPermission()
            <div class="col-sm-5">
        @else
            <div class="col-sm-10">
        @endallowedToChangeRoleAndPermission
            <h3>
                <small class="text-muted">Role details</small>
            </h3>
            <div>
                Name
            </div>
            <span style="font-size:0.75rem" id="error-name" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <span class="input-group-text text-danger mr-4">*</span>
                <input type="text" class="form-control" id="input-name" name="name" placeholder="name">
            </div>
        </div>
        @allowedToChangeRoleAndPermission()
        <div class="col-sm-5">
            <h3>
                <small class="text-muted">permissions</small>
            </h3>
            <select style="width: 100%" class="form-control select2" multiple="multiple" name="permissions[]">
                @foreach ($permissions as $permission)
                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                @endforeach
            </select>
        </div>
        @endallowedToChangeRoleAndPermission
        <div class="col-sm-1"></div>
    </div>
</form>

<script>
    $(function(yourcode) {
        yourcode(window.jQuery, window, document);
    }(function($, window, document) {
        $(function() {
            $(".select2").select2({
            });
        })
    }))
</script>
