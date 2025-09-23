@extends('dashboard.layouts.main')

@section('content')
  <main id="main" class="main">

    <div class="pagetitle">
      <a href="" class="btn btn-primary">Create New</a>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">ATK In</h5>

              <!-- Default Table -->
              <table class="table">
                <thead>
                  <tr>
                    <th class="text-center" scope="col">No</th>
                    <th class="text-center" scope="col">Description</th>
                    <th class="text-center" scope="col">Brand</th>
                    <th class="text-center" scope="col">Price</th>
                    <th class="text-center" scope="col">Stock</th>
                    <th class="text-center" scope="col">Location</th>
                    <th class="text-center" scope="col">Status</th>
                    <th class="text-center" scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th class="text-center" scope="row">1</th>
                    <td class="text-center">Designer</td>
                    <td class="text-center">Designer</td>
                    <td class="text-center">Designer</td>
                    <td class="text-center">Designer</td>
                    <td class="text-center">Designer</td>
                    <td class="text-center">28</td>
                    <td class="text-center">2016-05-25</td>
                  </tr>
                 
                </tbody>
              </table>
              <!-- End Default Table Example -->
            </div>
          </div>
        </div>
      </div>
    </section>

  </main><!-- End #main -->

  @endsection

 