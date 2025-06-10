@extends('layouts.app2')

@section('content')
    <div class="flex min-h-screen">
        @include('admin.partials.sidebar')

        <main class="flex-1 p-8 bg-gray-100">
            <h1 class="text-2xl font-bold mb-6">Felhasználók</h1>

            <div class="bg-white shadow rounded p-6">

                <form method="GET" class="mb-6 flex items-center gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Keresés név/email"
                           class="border px-3 py-2 rounded w-1/3">

                    <select name="is_admin" class="border px-2 py-2 rounded">
                        <option value="">-- Mindenki --</option>
                        <option value="1" {{ request('is_admin') === '1' ? 'selected' : '' }}>Csak adminok</option>
                        <option value="0" {{ request('is_admin') === '0' ? 'selected' : '' }}>Csak felhasználók</option>
                    </select>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Keresés
                    </button>
                </form>


                <table class="min-w-full table-auto text-sm">
                    <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-2">#</th>
                        <th class="p-2">Név</th>
                        <th class="p-2">Email</th>
                        <th class="p-2">Regisztrált</th>
                        <th class="p-2">Státusz</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b hover:bg-gray-100">
                            <td class="p-2">{{ $user->id }}</td>
                            <td class="p-2">
                                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:underline">
                                    {{ $user->name }}
                                </a>
                            </td>
                            <td class="p-2">{{ $user->email }}</td>
                            <td class="p-2">{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="p-2">
                                @if ($user->is_admin)
                                    <span class="text-green-600 font-semibold">Admin</span>
                                @else
                                    <span class="text-gray-600">Felhasználó</span>
                                @endif
                            </td>
                            <td class="p-2 space-x-2">
                                <form method="POST" action="{{ route('admin.users.toggleAdmin', $user) }}" class="inline">
                                    @csrf
                                    <button class="text-blue-600 hover:underline text-sm">Admin váltás</button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Biztosan törlöd?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline text-sm">Törlés</button>
                                </form>
                                <button type="button"
                                        onclick='showUserModal(@json($user))'
                                        class="text-indigo-600 hover:underline text-sm">
                                    👁 Megtekintés
                                </button>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </main>
    </div>
    <!-- Modal -->
    <div id="userModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-md max-w-md w-full relative">
            <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-400 hover:text-black text-lg">✖</button>

            <div class="flex flex-col items-center">
                <img id="userAvatar" src="" alt="Profilkép" class="w-24 h-24 rounded-full object-cover mb-4 shadow">
                <h2 class="text-xl font-bold mb-2" id="userName">Felhasználó neve</h2>
            </div>

            <div id="userDetails" class="text-sm space-y-2 mt-4 text-gray-700">
                <!-- JS tölti ki -->
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function showUserModal(user) {
                const modal = document.getElementById('userModal');
                const details = document.getElementById('userDetails');
                const avatar = document.getElementById('userAvatar');
                const nameEl = document.getElementById('userName');

                modal.classList.remove('hidden');

                nameEl.textContent = user.name;
                avatar.src = user.profile_picture
                    ? `/storage/${user.profile_picture}`
                    : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name);

                details.innerHTML = `
            <p><strong>Email:</strong> ${user.email}</p>
            <p><strong>Steam ID:</strong> ${user.steam_id ?? '—'}</p>
            <p><strong>Discord:</strong> ${user.discord_handle ?? '—'}</p>
            <p><strong>Admin státusz:</strong> ${user.is_admin ? '✔️ Admin' : 'Felhasználó'}</p>
            <p><strong>Regisztráció:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
        `;
            }

            function closeModal() {
                document.getElementById('userModal').classList.add('hidden');
            }
        </script>
    @endpush
@endsection
