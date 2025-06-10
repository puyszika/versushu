@extends('layouts.app2')

@section('content')
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main content -->
        <main class="flex-1 p-8 bg-gray-100">
            <h1 class="text-2xl font-bold mb-6">Admin Statisztika</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Felhasználók</div>
                    <div class="text-2xl font-bold">{{ $userCount }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Csapatok</div>
                    <div class="text-2xl font-bold">{{ $teamCount }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Bajnokságok</div>
                    <div class="text-2xl font-bold">{{ $championshipCount }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Összes regisztráció</div>
                    <div class="text-2xl font-bold">{{ $registrationCount }}</div>
                </div>
            </div>

            <!-- Aktivitások -->
            <div class="bg-white shadow rounded p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Legutóbbi aktivitások</h2>
                <ul class="space-y-2">
                    @foreach ($recentUsers as $user)
                        <li class="text-sm text-gray-700">🧑 {{ $user->name }} - regisztrált: {{ $user->created_at->diffForHumans() }}</li>
                    @endforeach
                    @foreach ($recentTeams as $team)
                        <li class="text-sm text-gray-700">🏆 {{ $team->name }} - létrehozva: {{ $team->created_at->diffForHumans() }}</li>
                    @endforeach
                    @foreach ($recentChampionships as $championship)
                        <li class="text-sm text-gray-700">📅 {{ $championship->name }} - létrehozva: {{ $championship->created_at->diffForHumans() }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Grafikon -->
            <div class="bg-white shadow rounded p-6">
                <h2 class="text-xl font-semibold mb-4">Havi regisztrációk</h2>
                <canvas id="userChart"></canvas>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('userChart').getContext('2d');
        const userChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Felhasználók száma',
                    data: @json($chartData),
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
        });
    </script>
@endpush
