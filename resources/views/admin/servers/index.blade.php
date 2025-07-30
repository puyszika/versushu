@extends('layouts.app2')

@section('content')
    <div class="container">
        <h2>Szerverek kezelése</h2>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th><th>IP</th><th>Port</th><th>Status</th><th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($servers as $server)
                <tr>
                    <td>{{ $server->id }}</td>
                    <td>{{ $server->ip }}</td>
                    <td>{{ $server->port }}</td>
                    <td>{{ $server->status }}</td>
                    <td>
                        @if($server->status === 'available')
                            <form action="{{ route('admin.servers.start', $server->id) }}" method="POST" style="display:inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Indítás</button>
                            </form>
                        @else
                            <form action="{{ route('admin.servers.stop', $server->id) }}" method="POST" style="display:inline">
                                @csrf
                                <button class="btn btn-danger btn-sm">Leállítás</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
