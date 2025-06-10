@extends('layouts.app2')

@section('content')
    <div class="flex min-h-screen">
        @include('admin.partials.sidebar')

        <div class="p-6 flex-1">
            <h2 class="text-2xl font-bold mb-6">Csapatok</h2>

            <!-- Kereső -->
            <form method="GET" action="{{ route('admin.teams.index') }}" class="mb-4 flex gap-2">
                <input type="text" name="search" placeholder="Keresés név alapján"
                       value="{{ request('search') }}"
                       class="border rounded px-3 py-2 w-1/2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Keresés
                </button>
            </form>

            <!-- Táblázat -->
            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Név</th>
                        <th class="px-4 py-2 text-left">Leírás</th>
                        <th class="px-4 py-2 text-left">Státusz</th>
                        <th class="px-4 py-2 text-left">Műveletek</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @foreach ($teams as $team)
                        <tr>
                            <td class="px-4 py-2">{{ $team->id }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('teams.show', $team->id) }}" class="text-blue-600 hover:underline">
                                    {{ $team->name }}
                                </a>
                            </td>
                            <td class="px-4 py-2">{{ Str::limit($team->description, 50) }}</td>
                            <td class="px-4 py-2">
                                {{ $team->members->count() }} tag
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <!-- Megtekintés -->
                                <button onclick="openModal({{ $team->id }})"
                                        class="text-sm text-blue-600 hover:underline">
                                    👁 Megtekintés
                                </button>

                                <!-- Törlés -->
                                <form method="POST" action="{{ route('admin.teams.destroy', $team->id) }}"
                                      class="inline"
                                      onsubmit="return confirm('Biztosan törlöd a csapatot?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:underline">
                                        🗑 Törlés
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div id="modal-{{ $team->id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                            <div class="bg-white w-full max-w-md p-6 rounded shadow-lg relative">
                                <button onclick="closeModal({{ $team->id }})" class="absolute top-2 right-3 text-gray-500">✖</button>
                                <h3 class="text-xl font-bold mb-2">{{ $team->name }}</h3>
                                @if($team->logo_path)
                                    <img src="{{ asset('storage/' . $team->logo_path) }}" class="w-full rounded mb-2">
                                @endif
                                <p class="mb-2">{{ $team->description }}</p>
                                <strong>Tagok:</strong>
                                <ul class="list-disc pl-5">
                                    @foreach ($team->members as $member)
                                        <li>{{ $member->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagináció -->
            <div class="mt-4">
                {{ $teams->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openModal(id) {
            document.getElementById('modal-' + id).classList.remove('hidden');
            document.getElementById('modal-' + id).classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById('modal-' + id).classList.add('hidden');
            document.getElementById('modal-' + id).classList.remove('flex');
        }
    </script>
@endpush
