<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Process;
use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Cache;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {

    // Log::build([
    //     'driver' => 'single',
    //     'path' => storage_path('logs/custom.log'),
    //   ])->info('Something happened!');

    //Log::info('Dashboard entered');
    //Log::emergency('The system is down!');
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::get('/openai', function (){

    $result = OpenAI::images()->create([
        "prompt"=>"A cute baby sea otter",
        "n" => 2,
        "size" => "512x512"
    ]);

    return response(['url' => $result->data[0]->url]);

    // $result = OpenAI::completions()->create([
    //     'model' => 'text-davinci-003',
    //     'prompt' => 'PHP is',
    // ]);

    // echo $result['choices'][0]['text']; // an open-source, widely-used, server-side scripting language.
});

Route::get('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
});

Route::get('/auth/callback', function () {
    $user = Socialite::driver('github')->user();
    dd($user);
    // $user->token
});

Route::get('/processtest', function () {


    $result = Process::run('pwd');

    return $result->output();
});


Route::get('send-mail', [MailController::class, 'index']);


Route::get('/chart', function () {
    return view('chart');
});

Route::get('/plotly-chart', function () {
    return view('plotlyChart');
});

Route::get('/php-network', function () {

    $domain="google.com";
    if(checkdnsrr($domain,"MX")) {
        echo "Passed";
    } else {
        echo "Failed";
    }

    echo "<br>";

    $hostlist = gethostbynamel("naver.com");
    print_r($hostlist);

    echo "<br>";

    echo gethostname();

    echo "<br>";


});

Route::get('/abort-test', function () {
    abort(404);

    //abort(403, 'Unauthorized action.');
});

Route::get('/test', 'App\Http\Controllers\PusherController@index');
Route::post('/broadcast', 'App\Http\Controllers\PusherController@broadcast');
Route::post('/receive', 'App\Http\Controllers\PusherController@receive');

Route::get('cache-test', function () {

    //cache flush
    //Cache::flush();
    Cache::put('user', 'John', 10);

    // if(class_exists('Memcache')){
    //     // Memcache is enabled.
    //     echo "Memcache is enabled";
    //   }else{
    //     // Memcache is not enabled.
    //     echo "Memcache is not enabled";
    //   }
    dd(Cache::get('user'));
});


Route::any('captcha-test', function() {
    if (request()->getMethod() == 'POST') {
        $rules = ['captcha' => 'required|captcha'];
        $validator = validator()->make(request()->all(), $rules);
        if ($validator->fails()) {
            echo '<p style="color: #ff0000;">Incorrect!</p>';
        } else {
            echo '<p style="color: #00ff30;">Matched :)</p>';
        }
    }

    $form = '<form method="post" action="captcha-test">';
    $form .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    $form .= '<p>' . captcha_img() . '</p>';
    $form .= '<p><input type="text" name="captcha"></p>';
    $form .= '<p><button type="submit" name="check">Check</button></p>';
    $form .= '</form>';
    return $form;
});