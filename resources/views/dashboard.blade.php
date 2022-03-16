<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    @push('js')
      @include('js')
    @endpush
    {{-- <div class="card m-3">
        <div class="card-header">
            <h3 class="card-title">Location</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body mb-20">
          <div class="form-group">
              <label for="email">Current Location</label>
              <input type="text" class="form-control" name="location" id="location" readonly>
          </div>
          <div id="map"></div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer text-center" style="color: rgba(0,0,0,.03)">
            Footer
        </div>
        <!-- /.card-footer-->
    </div> --}}
    
    <div class="card m-3">
        <div class="card-header">
          <h3 class="card-title">Employee List</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <div class="text-left m-3">
            <a href="{{ route('user.create') }}">
              <button class="btn btn-xl btn-info text-bold"><i class="fas fa-user-plus mr-2"></i>Add new</button>
            </a>
          </div>
          <table id="user_table" class="table table-bordered table-striped table-hover text-center">
            <thead>
              <tr>
                <th>Action</th>
                <th>Name</th>
                <th>Email</th>
                <th>Creation date</th>
              </tr>
            </thead>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
</x-app-layout>
