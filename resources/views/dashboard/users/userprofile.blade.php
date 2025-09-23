@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Profile Users</h5>

                            <!-- Horizontal Form -->
                            <form action="#" method="POST">
                                @method('PUT')
                                {{ csrf_field() }}
                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputText" name="name" required
                                            value="{{ $users->name }}" disabled>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="inputEmail" name="email" required
                                            value="{{ $users->email }}" disabled>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputState" class="col-sm-2 col-form-label">Role</label>
                                    <div class="col-sm-10">
                                        <select id="inputState" class="form-select" name="is_role" disabled>
                                            <option selected value="">Choose Role</option>
                                            <option value="0" {{ $users->is_role == 0 ? 'selected' : '' }}>Users
                                            </option>
                                            <option value="1" {{ $users->is_role == 1 ? 'selected' : '' }}>Admin
                                            </option>
                                            <option value="2" {{ $users->is_role == 2 ? 'selected' : '' }}>Super Admin
                                            </option>
                                            {{-- <option value="0">Users</option>
                                            <option value="1">Admin</option>
                                            <option value="2">Super Admin</option> --}}
                                        </select>
                                    </div>
                                </div>
                            </form><!-- End Horizontal Form -->

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection
