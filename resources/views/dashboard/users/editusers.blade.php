@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Edit Users</h5>

                            <!-- Horizontal Form -->
                            <form action="{{ route('updateuserspost', $users->id)}}" method="POST">
                                @method("PUT") 
                                {{ csrf_field() }}
                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Name<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputText" name="name" required value="{{ $users->name }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Email<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="inputEmail" name="email"  required value="{{ $users->email }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputPassword3" class="col-sm-2 col-form-label">Change Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="inputPassword" name="password">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputPassword3" class="col-sm-2 col-form-label">Confirm Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="inputPassword" name="confirm_password">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputState" class="col-sm-2 col-form-label">Select Role<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <select id="inputState" class="form-select" name="is_role" required>
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

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Save</button>
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
