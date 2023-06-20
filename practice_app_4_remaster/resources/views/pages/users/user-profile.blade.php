<x-layout bodyClass="g-sidenav-show bg-gray-200">

    <x-navbars.sidebar activePage="user-profile"></x-navbars.sidebar>
    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage='User Profile'></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
                <span class="mask  bg-gradient-primary  opacity-6"></span>
            </div>
            <div class="card card-body mx-3 mx-md-4 mt-n6">
                <div class="row gx-4 mb-2">
                    <div class="col-auto">
                        <div class="avatar avatar-xl position-relative">
                            <img src="{{ asset('assets') }}/img/bruce-mars.jpg" alt="profile_image"
                                class="w-100 border-radius-lg shadow-sm">
                        </div>
                    </div>
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1" id="profile-name">
                                {{ auth()->user()->name }}
                            </h5>
                            <span class="text-primary h6">Roles:</span>
                            <p class="mb-0 font-weight-normal text-sm">
                                @if (count(auth()->user()->roles) > 0)
                                    @foreach (auth()->user()->roles as $role)
                                        <span class="badge bg-primary"> {{ $role->name }} </span>
                                    @endforeach
                                @else
                                    none
                                @endif
                            </p>
                            <span class="text-primary h6">All permissions:</span>
                            <p class="mb-0 font-weight-normal text-sm">
                                @if (count(auth()->user()->getAllPermissionNames()) > 0)
                                    @foreach (auth()->user()->getAllPermissionNames() as $name)
                                        <span class="badge bg-info"> {{ $name }} </span>
                                    @endforeach
                                @else
                                    none
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card card-plain h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-md-8 d-flex align-items-center">
                                <h6 class="mb-3">Profile Information</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @if (session('status'))
                            <div class="row">
                                <div class="alert alert-success alert-dismissible text-white" role="alert">
                                    <span class="text-sm">{{ Session::get('status') }}</span>
                                    <button type="button" class="btn-close text-lg py-3 opacity-10"
                                        data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                        @if (Session::has('demo'))
                            <div class="row">
                                <div class="alert alert-danger alert-dismissible text-white" role="alert">
                                    <span class="text-sm">{{ Session::get('demo') }}</span>
                                    <button type="button" class="btn-close text-lg py-3 opacity-10"
                                        data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                        <form method='POST' action='{{ route('user-profile.update') }}' id="form-data"
                            data-method="put">
                            @csrf
                            <div class="row">

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Email address</label>
                                    <input type="email" name="email" class="form-control border border-2 p-2"
                                        value='{{ old('email', auth()->user()->email) }}' id="input-email">
                                    <p class='text-danger error' id="error-email"></p>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control border border-2 p-2"
                                        value='{{ old('name', auth()->user()->name) }}' id="input-name">
                                    <p class='text-danger error' id="error-name"></p>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="number" name="phone" class="form-control border border-2 p-2"
                                        value='{{ old('phone', auth()->user()->phone) }}' id="input-phone">
                                    <p class='text-danger error' id="error-phone"></p>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" class="form-control border border-2 p-2"
                                        value='{{ old('location', auth()->user()->location) }}' id="input-location">
                                    <p class='text-danger error' id="error-location"></p>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Update your password</label>
                                    <input type="text" name="password" class="form-control border border-2 p-2"
                                        id="input-password">
                                    <p class='text-danger error' id="error-password"></p>
                                </div>

                                <div class="mb-3 col-md-12">
                                    <label for="floatingTextarea2">About</label>
                                    <textarea class="form-control border border-2 p-2" placeholder=" Say something about yourself" id="floatingTextarea2"
                                        name="about" rows="4" cols="50" id="input-about">{{ old('about', auth()->user()->about) }}</textarea>
                                    <p class='text-danger error' id="error-about"></p>
                                </div>
                            </div>
                            <button type="submit"
                                class="btn bg-gradient-dark button-update profile-button-confirm">Submit</button>
                        </form>

                    </div>
                </div>
            </div>
            <script
                src="{{ asset('assets/models/users/profileFormTrigger.js') }}?v={{ filemtime(public_path('assets/models/users/profileFormTrigger.js')) }}">
            </script>
        </div>
        <x-footers.auth></x-footers.auth>
    </div>
    <x-plugins></x-plugins>

</x-layout>
