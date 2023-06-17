<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage="role-management"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Role Management"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <!-- Modal -->
        @include('components.modals.form-modal', ['title' => ''])
        <!-- End Modal -->
        <div class="container-fluid py-4">
            <div class="row">
                <form class="row" id="form-search" action="{{ route('roles.search') }}" method="GET">
                    @csrf
                    @method('GET')
                    <div class="col-md-4">
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control border" name="name">
                            <div class="input-group-append mx-3">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline my-3">
                            <select class="form-select" aria-label="Role">
                                <option selected>Select a Permission</option>
                                <option value="admin">perm 1</option>
                                <option value="user">perm 2</option>
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
                                        <strong>Role table</strong>
                                    </div>
                            </div>
                            <div class=" me-3 my-3 text-end">
                                <a class="btn bg-gradient-dark mb-0" href="javascript:;"><i
                                        class="material-icons text-sm">add</i>&nbsp;&nbsp;Create New
                                    Role</a>
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
<script
    src="{{ asset('assets/models/common/domTrigger.js') }}?v={{ filemtime(public_path('assets/models/common/domTrigger.js')) }}">
</script>
