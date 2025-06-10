<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ChampionshipController;
use App\Http\Controllers\ChampionshipRegistrationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PostController;
use App\Models\Post;
use App\Http\Controllers\CommentController;
use App\Services\GoogleOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LobbyController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MatchmakingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// === Publikus oldalak ===
Route::get('/', fn () => view('welcome'))->name('welcome');

Route::get('/home', function () {
    $posts = Post::latest()->paginate(6);
    return view('home', compact('posts'));
})->name('home');

Route::get('/info', fn () => view('info'))->name('info');
Route::get('/contact', fn () => view('contact'))->name('contact');
Route::get('/partners', fn () => view('partners'))->name('partners');

Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [PostController::class, 'show'])->name('blog.show');

Route::get('/ocr-teszt', function () {
    return view('ocr-test'); // ezt is létrehozzuk
});

Route::get('/ocr-teszt', function () {
    return view('ocr-test'); // form betöltése üresen
});

Route::post('/ocr-teszt', function (Request $request, GoogleOcrService $ocr) {
    logger('✅ POST route elindult');

    $request->validate([
        'image' => 'required|image|max:5120',
    ]);

    $path = $request->file('image')->store('ocr_test_images');
    $fullPath = storage_path('app/' . $path);

    logger("📸 Kép elmentve ide: $fullPath");

    $structured = $ocr->extractStructuredData($fullPath);

    logger('📊 Feldolgozott OCR eredmények:', $structured);

    return view('ocr-test', [
        'structuredData' => $structured,
    ]);
});

Route::get('/session-test', function (\Illuminate\Http\Request $request) {
    return [
        'session_has' => $request->session()->has('_token'),
        'csrf_token' => csrf_token(),
        'session_id' => $request->session()->getId(),
        'auth_user' => Auth::check() ? Auth::user()->email : null,
        'guard' => Auth::getDefaultDriver(),
    ];
});




// --- Auth + Felhasználói oldalak ---
Route::middleware(['auth'])->group(function () {



    // Profilkezelés
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Csapatok (felhasználói oldal)
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/join', [TeamController::class, 'join'])->name('teams.join');
    Route::post('/teams/join', [TeamController::class, 'handleJoin'])->name('teams.handleJoin');
    Route::post('/teams/leave', [TeamController::class, 'leave'])->name('teams.leave');
    Route::post('/teams/transfer-ownership', [TeamController::class, 'transferOwnership'])->name('teams.transferOwnership');
    Route::delete('/teams/delete', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::get('/teams/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::post('/teams/update', [TeamController::class, 'update'])->name('teams.update');
    Route::post('/teams/kick/{user}', [TeamController::class, 'kick'])->name('teams.kick');
    Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');


    // Felhasználói profil megtekintés (más felhasználó)
    Route::get('/users/{user}', [ProfileController::class, 'show'])->name('users.show');

    // Bajnokságok - Publikus nézet
    Route::get('/championships', [ChampionshipController::class, 'publicIndex'])->name('championships.public');
    Route::get('/championships/{championship}', [ChampionshipController::class, 'publicShow'])->name('championships.show');
    Route::get('/championships/{championship}/bracket', [ChampionshipController::class, 'publicBracket'])->name('championships.bracket');
    Route::post('/championships/{championship}/register', [ChampionshipRegistrationController::class, 'store'])->name('championships.register');

    Route::post('/matches/{match}/submit-result', [ChampionshipController::class, 'submitResult'])->name('matches.submitResult');
    Route::post('/matches/{match}/verify', [ChampionshipController::class, 'verifyResult'])->name('matches.verifyResult');

    // post v. blog - publikus nézet
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');


    Route::get('/lobby/{code}', [LobbyController::class, 'show'])->name('lobby.show');
    Route::get('/matchmaking', [MatchmakingController::class, 'index']);

});

// --- Admin szekció ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard + felhasználók
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggleAdmin');


    // Csapatok
    Route::get('/teams', [TeamController::class, 'adminIndex'])->name('teams.index');
    Route::delete('/teams/{team}', [TeamController::class, 'adminDestroy'])->name('teams.destroy');
    Route::resource('sliders', App\Http\Controllers\Admin\SliderController::class)->only(['index', 'store', 'destroy']);

    // Bajnokságok (Admin funkciók)
    Route::resource('championships', ChampionshipController::class);
    Route::post('/championships/{championship}/start', [ChampionshipController::class, 'startTournament'])->name('championships.start');
    Route::post('/championships/{championship}/start-tournament', [ChampionshipController::class, 'startTournament'])->name('championships.startTournament');
    Route::post('/championships/{championship}/finish', [ChampionshipController::class, 'finish'])->name('championships.finish');
    Route::get('/championships/{championship}/bracket', [ChampionshipController::class, 'bracket'])->name('championships.bracket');
    Route::post('/championships/{championship}/set-mvp', [ChampionshipController::class, 'setMvp'])->name('championships.setMvp');
    Route::delete('/championships/{championship}/teams/{team}', [ChampionshipController::class, 'detachTeam'])->name('championships.teams.detach');




    // Meccsgyőztes beállítása
    Route::post('/matches/{match}/verify', [ChampionshipController::class, 'verifyResult'])->name('matches.verify');



    Route::get('/posts', [PostController::class, 'adminIndex'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');



});

// Auth route-ok
require __DIR__.'/auth.php';
