<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user == 'none' ? 'Add' : 'Edit' }}
        </h2>
    </x-slot>

    @push('js')
        @include('js')
    @endpush
    <div class="card card-primary m-3">
        <div class="card-header">
          <h3 class="card-title">{{ $user == 'none' ? 'Add' : 'Edit' }} Details</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form method="post" action="{{ route('user.store') }}" id="user_form" autocomplete="off">
            <input type="text" name="id" id="id" value="{{ $user == 'none' ? 'none' : $user->id }}" hidden>
            <input type="text" name="process" id="process" value="{{ $user == 'none' ? 'Add' : 'Edit' }}" hidden>
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ $user == 'none' ? '' : $user->name }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" name="email" id="email" value="{{ $user == 'none' ? '' : $user->email }}" required>
                </div>
                @if ($user == 'none')
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                </div>
                @endif
                <div class="form-group">
                    <span id="store_image"></span>
                    <img src="{{ $user == 'none' ? asset('storage/pics/blank.PNG' ) : asset('storage/pics/'. $user->picture ) }}" id="blah" alt="your image" style="width:150px; height:150px;"/>      
                    <input type="text" class="form-control" name="pictureOld" id="pictureOld" value="{{ $user == 'none' ? '' : $user->picture }}" hidden>
                    <br>
                    <input type="file" name="picture" id="picture" onchange="readURL(this);">
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn text-bold">Submit</button>
            </div>
        </form>
    </div>

</x-app-layout>
