<?php

Route::get('/debug-session', function () {
    return response()->json([
        'session_driver' => config('session.driver'),
        'session_path' => config('session.path'),
        'session_lifetime' => config('session.lifetime'),
        'session_domain' => config('session.domain'),
        'session_secure' => config('session.secure'),
        'session_http_only' => config('session.http_only'),
        'session_same_site' => config('session.same_site'),
        'storage_sessions_path' => storage_path('framework/sessions'),
        'sessions_dir_exists' => is_dir(storage_path('framework/sessions')),
        'sessions_dir_writable' => is_writable(storage_path('framework/sessions')),
        'app_key_present' => !empty(config('app.key')),
        'csrf_token_sample' => csrf_token(),
    ]);
});
