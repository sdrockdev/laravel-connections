<?php

require_once __DIR__.'/../../../vendor/autoload.php';

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->router->post('connect-success', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'Successfully created 9',
        'code' => 201,
        'data' => (object)[
            'item' => (object)[
                'name' => app('request')->input('name'),
                'email' => app('request')->input('email'),
                'platform_id' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'id' => 9,
            ],
        ]
    ]);
});

$app->router->post('connect-platform-does-not-exist', function() {
    return response()->json([
        'status' => 'fail',
        'message' => 'Invalid submission',
        'code' => 422,
        'data' => (object)[
            'errors' => [
                'Platform id does not exist',
            ],
        ]
    ]);
});

$app->router->post('connect-server-will-fail', function() {
  abort(500);
});



$app->run();
