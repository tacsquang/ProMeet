<?php

use App\Core\Container;
use App\Core\Database;
use App\Core\LogService;
use App\Models\UserModel;
use App\Models\RoomModel;
use App\Models\ReviewModel;
use App\Models\BookingModel;
use App\Models\ImageModel;

$container = Container::getInstance();


// Đăng ký các service singleton
$container->set('db', function () {
    return Database::getInstance(); // Singleton
});

$container->set('logger', function () {
    return LogService::getInstance(); // Singleton
});


// Đăng ký các model (có thể dùng lại db singleton)
$container->set('UserModel', function ($c) {
    return new UserModel(
        $c->get('db'),
        $c->get('logger')
    );
});

$container->set('RoomModel', function ($c) {
    return new RoomModel(
        $c->get('db'),
        $c->get('logger')
    );
});

$container->set('BookingModel', function ($c) {
    return new BookingModel(
        $c->get('db'),
        $c->get('logger')
    );
});

$container->set('ReviewModel', function ($c) {
    return new ReviewModel(
        $c->get('db'),
        $c->get('logger')
    );
});

$container->set('ImageModel', function ($c) {
    return new ImageModel(
        $c->get('db'),
        $c->get('logger')
    );
});
