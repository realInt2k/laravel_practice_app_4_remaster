<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage="product-management"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Product Management"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <form class="row" id="form-search" action="{{ route('products.search') }}" method="GET">
                    @csrf
                    @method('GET')
                    <div class="col-md-4">
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">🔍 Name</label>
                            <input type="text" class="form-control border search-input" name="name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">🔍 Description</label>
                            <input type="text" class="form-control border search-input" name="description">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline my-3">
                            <select class="form-select" aria-label="category_id">
                                <option selected>Select a category</option>
                                <option value="1">cat 1</option>
                                <option value="2">cat 2</option>
                            </select>

                        </div>
                    </div>
                </form>
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white mx-3">
                                    <div>
                                        <strong>Product table</strong>
                                    </div>
                            </div>
                            
                            <div class="card-body px-0 pb-2">
                                <div class="table-responsive p-0">
                                    <div id="table-data"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <x-footers.auth></x-footers.auth>
            </div>
    </main>
    <x-plugins></x-plugins>

</x-layout>
