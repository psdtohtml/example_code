<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'rebate' => [
            'class' => 'app\module\rebate\Module',
            'as access' => [ // if you need to set access
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'] // all auth users
                    ],
                ]
            ],
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => '1234zZmk7b123Q0JX9oSL1w2uIEy7ISe',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

    ],
    'aliases' => [
        '@avatar' => '@frontend/web/uploads/avatar/',
    ],
    'params' => $params,
    'on afterRequest' => function ($event) {
        if(isset(Yii::$app->request->queryParams['ref'])) {            
            $cookies = Yii::$app->response->cookies;            
            $cookies->add(new \yii\web\Cookie([
                'name' => 'referral',
                'value' => Yii::$app->request->queryParams['ref'],
                'expire' => time() + 60*60*24*10,
            ]));
        }
    }
];
