@extends('layouts.app')

@section('content')
    <div class="container pt-5">
        <div class="mb-5">

            <h1>User list</h1>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>id</th>
                    <th>name</th>
                    <th>email</th>
                    <th>phone</th>
                    <th>position</th>
                    <th>position_id</th>
                    <th>registration_timestamp</th>
                    <th>photo</th>
                </tr>
                </thead>
                <tbody>
                @foreach($usersPaginator as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->position->title }}</td>
                    <td>{{ $user->position_id }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>
                        @if($user->photo && !str_contains($user->photo, '__raw__'))
                            <img src="{{ url('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="img-fluid">
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-center">
                @if(!$usersPaginator->onFirstPage())
                <a class="btn btn-link" href="{{ $usersPaginator->previousPageUrl() }}"><< Previous page</a>
                @endif
                @if($usersPaginator->hasMorePages())
                    <a class="btn btn-link" href="{{ $usersPaginator->nextPageUrl() }}">Next page >></a>
                @endif
            </div>
        </div>

        <div class="col-lg-6 offset-lg-3 mb-5">
            <h2>Add user</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="formName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="formName" name="name" value="{{ old('name') }}">
                </div>
                <div class="mb-3">
                    <label for="formEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="formEmail" name="email" value="{{ old('email') }}">
                </div>
                <div class="mb-3">
                    <label for="formPhone" class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="formPhone"
                           name="phone" value="{{ old('phone') }}">
                </div>
                <div class="mb-3">
                    <label for="formPosition" class="form-label">Position</label>
                    <select class="form-select" id="formPosition" name="position_id">
                        <option selected>Open this select menu</option>
                        @foreach($positions as $position)
                            @if(old('position_id') == $position->id)
                                <option value="{{ $position->id }}" selected>
                                    {{ $position->title }}
                                </option>
                            @else
                                <option value="{{ $position->id }}">
                                    {{ $position->title }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="formFile" class="form-label">Photo</label>
                    <input class="form-control" type="file" id="formFile" name="photo">
                </div>

                <div class=text-center>
                    <button type="submit" class="btn btn-success btn-lg">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
