<form id="form-data" action="{{ route('products.store') }}" method="post" data-method="post" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-5">
            <h3>
                <small class="text-muted">Product details</small>
            </h3>

            <div class="text-primary">
                <strong>Name:</strong>
            </div>
            <span style="font-size:0.75rem" id="error-name" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <span class="input-group-text text-danger mr-4">*</span>
                <input type="text" class="form-control" id="input-name" name="name" placeholder="name">
            </div>

            <div class="text-primary">
                <strong>Description:</strong>
            </div>
            <span style="font-size:0.75rem" id="error-description" class="error text-danger"></span>
            <div class="input-group input-group-dynamic mb-4">
                <textarea class="form-control" id="input-description" name="description" 
                placeholder="description" value=""></textarea>
            </div>

        </div>
        <div class="col-sm-5">
            <h3>
                <small class="text-muted">Category assignment</small>
            </h3>
            <select style="width: 100%" class="select2" multiple="multiple" name="category_ids[]">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->name }}</option>
                @endforeach
            </select>

            <h3>
                <small class="text-muted">Upload an image 🖼️</small>
            </h3>
            <span style="font-size:0.75rem" id="error-image" class="error text-danger"></span>
            <!-- HTML code for the image upload button -->
            <div class="input-group mb-3">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="image-input" name="image">
                </div>
            </div>

            <!-- HTML code for the image preview -->
            <div id="image-preview" class="position-relative" hidden>
                <img id="preview-image" class="img-fluid rounded mb-2" src="#">
                <button id="delete-image" class="btn btn-danger position-absolute top-0 start-0">x</button>
                <input type='hidden' name='remove_image_request' id='remove-image-request' val='false'>
            </div>
        </div>
        <div class="col-sm-1"></div>
    </div>
</form>

<script
    src="{{ asset('assets/models/products/modalTrigger.js') }}?v={{ filemtime(public_path('assets/models/products/modalTrigger.js')) }}">
</script>
