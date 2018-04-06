<?php
// DIC configuration

$container = $app->getContainer();
$development = $_SERVER['REMOTE_ADDR'] === '127.0.0.1' ? true : false;

// view renderer (Twig templates)
$container['view'] = function ($container) {
    $settings = $container->get('settings')['view'];
    $view = new \Slim\Views\Twig($settings['template_path'], [
        'debug' => true, // TODO: Remove in production
        'cache' => $settings['cache_path'],
    ]);
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    $view->addExtension(new \Twig_Extension_Debug()); // TODO: Remove in production
    return $view;
};

// monolog
$container['logger'] = function ($container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// pdo
$container['db'] = function ($container) {
    $settings = $container->get('settings')['db'];
    $db = new PDO($settings['dsn'], $settings['user'], $settings['pass']);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $db;
};

// constants
$container['constants'] = function ($container) {
    return require_once __DIR__ . '/constants.php';
};

// queries
$container['queries'] = function ($container) {
    $db = $container->db;

    $raw_queries = require_once __DIR__ . '/queries.php';
    $queries = [];
    foreach ($raw_queries as $model => $model_queries) {
        $queries[$model] = [];
        foreach ($model_queries as $query_name => $query) {
            $queries[$model][$query_name] = $db->prepare($query);
        }
    }
    return $queries;
};

// mail
$container['mail'] = function ($container) {
    return function () use ($container) {
        $settings = $container->get('settings')['mail'];
        $mail = new PHPMailer;
        $mail->setFrom($settings['from']);
        if (isset($settings['replyTo'])) {
            $mail->addReplyTo($settings['replyTo']);
        }
        if (isset($settings['smtp'])) {
            $mail->isSMTP();
            $mail->Host = $settings['smtp']['host'];
            $mail->Port = $settings['smtp']['port'];
            if (isset($settings['smtp']['auth'])) {
                $mail->SMTPAuth = true;
                $mail->Username = $settings['smtp']['auth']['user'];
                $mail->Password = $settings['smtp']['auth']['pass'];
                if ($settings['smtp']['secure']) {
                    $mail->SMTPSecure = $settings['smtp']['secure'];
                }
            } else {
                $mail->SMTPAuth = true;
            }
        }
        return $mail;
    };
};

// csrf guard
$container['csrf'] = function ($container) {
    session_name('assetlib-csrf');
    session_start();
    return new \Slim\Csrf\Guard;
};

// cookies
$container['cookies'] = function ($container) {
    return [
        'cookie' => function ($name, $value) {
            return Dflydev\FigCookies\Cookie::create($name, $value);
        },
        'setCookie' => function ($name) {
            return Dflydev\FigCookies\SetCookie::create($name);
        },
        'requestCookies' => new Dflydev\FigCookies\FigRequestCookies,
        'responseCookies' => new Dflydev\FigCookies\FigResponseCookies,
    ];
};

// tokens
$container['tokens'] = function ($container) {
    return new Godot\AssetLibrary\Helpers\Tokens($container);
};

// utils
$container['utils'] = function ($container) {
    return new Godot\AssetLibrary\Helpers\Utils($container);
};

// controllers
$container['AssetController'] = function ($c) {
    return new Godot\AssetLibrary\Controllers\AssetController($c);
};
$container['AssetEditController'] = function ($c) {
    return new Godot\AssetLibrary\Controllers\AssetEditController($c);
};
$container['UserController'] = function ($c) {
    return new Godot\AssetLibrary\Controllers\UserController($c);
};
$container['AuthController'] = function ($c) {
    return new Godot\AssetLibrary\Controllers\AuthController($c);
};
