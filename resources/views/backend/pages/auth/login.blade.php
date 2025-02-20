<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modernize Free</title>
    <link
        rel="stylesheet"
        href="{{ asset('backend/src/assets/images/logos/favicon.png') }}" />
    <link
        rel="stylesheet"
        href="{{ asset('backend/src/assets/css/styles.min.css') }}" />
</head>

<body>
    <!--  Body Wrapper -->
    <div
        class="page-wrapper"
        id="main-wrapper"
        data-layout="vertical"
        data-navbarbg="skin6"
        data-sidebartype="full"
        data-sidebar-position="fixed"
        data-header-position="fixed">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div
                class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-4">
                        <div class="card mb-0">
                            <div class="card-body">
                                <h3 class="text-center fw-bolder">
                                    Login Petugas
                                </h3>
                                <p class="text-center">
                                    Silahkan untuk login sebagai petugas
                                </p>
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                                @endif

                                <form
                                    action="{{ route('login') }}"
                                    method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label
                                            for="email"
                                            class="form-label">Email</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="email"
                                            name="email"
                                            required placeholder="Masukkan Username"/>
                                    </div>
                                    <div class="mb-4">
                                        <label
                                            for="password"
                                            class="form-label">Password</label>
                                        <input
                                            type="password"
                                            class="form-control"
                                            id="password"
                                            name="password"
                                            required placeholder="Masukkan Password"/>
                                    </div>
                                    <div
                                        class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            <input
                                                class="form-check-input primary"
                                                type="checkbox"
                                                value="1"
                                                id="remember"
                                                name="remember" />
                                            <label
                                                class="form-check-label text-dark"
                                                for="remember">
                                                Remember this Device
                                            </label>
                                        </div>
                                    </div>
                                    <button
                                        type="submit"
                                        class="btn btn-primary w-100 py-8 fs-4 rounded-2">
                                        Sign In
                                    </button>
                                    <div
                                        class="d-flex align-items-center justify-content-center">
                                        {{-- <p class="fs-4 mb-0 fw-bold">
                                            New to Modernize?
                                        </p> --}}
                                        {{-- <a
                                            class="text-primary fw-bold ms-2"
                                            href="{{ route('register') }}">Create an account</a> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('backend/src/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/src/assassets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>