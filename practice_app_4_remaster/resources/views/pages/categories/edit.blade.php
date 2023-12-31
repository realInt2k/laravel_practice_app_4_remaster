<form id="form-data" action="{{ route('categories.update', $category->id) }}" method="post" data-method="put">
    @csrf
    @method('put')
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-5">
            <h3>
                <small class="text-muted">Category details</small>
            </h3>
            <div>
                Name
            </div>
            <span style="font-size:0.75rem" id="error-name" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <span class="input-group-text text-danger mr-4">*</span>
                <input type="text" class="form-control" id="input-name" name="name" placeholder="name"
                    value="{{ $category->name }}">
            </div>
        </div>
        <div class="col-sm-5">
            <h3>
                <small class="text-muted">parent category</small>
            </h3>
            <select style="width: 100%" class="form-control custom_select2" name="parent_id">
                <option value="">no parent category</option>
                @foreach ($categories as $cat)
                    <option {{ $category->parent_id == $cat->id ? 'selected' : '' }}
                        value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <span style="font-size:0.75rem" id="error-parent_id" class="error text-danger"></span>
        </div>
        <div class="col-sm-1"></div>
    </div>
</form>

<script>
    $(function(yourcode) {
        yourcode(window.jQuery, window, document);
    }(function($, window, document) {
        $(function() {
            $(".custom_select2").select2({
                dropdownParent: $("#form-modal")
            });
        })
    }))
</script>
