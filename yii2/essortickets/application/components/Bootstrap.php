<?php

namespace app\components;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        // Here you can refer to Application object through $app variable
        $app->params['uploadPath'] = $app->basePath . '/../web/uploads/';
        $app->params['uploadUrl'] = $app->urlManager->baseUrl . '/../web/uploads/';
        $app->params['templatePdfPath'] = $app->basePath . '/../templates/';
        $app->params['templatePdfUrl'] = $app->urlManager->baseUrl . '/../templates/';
        $app->params['imgPath'] = $app->basePath . '/../web/img/';
        $app->params['imgUrl'] = $app->urlManager->baseUrl . '/../web/img/';
    }
}