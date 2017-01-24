<?php
use frontend\assets\VpluseAsset;
use yii\helpers\Html;
use common\widgets\Alert;

VpluseAsset::register($this);
$this->beginPage();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <!--[if IE]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="layer"></div>
        <div class="wrapper">
            <!-- BEGIN HEADER -->
            <?= $this->render('//site/head') ?>
            <!-- HEADER EOF -->

            <!-- BEGIN SIDEBAR -->
            <?= $this->render('//site/left') ?>
            <!-- SIDEBAR EOF -->
            <!-- BEGIN CONTENT -->
            <section class="content">
                <div class="block__container">
                    <div class="content__main">
                        <div class="content__section">
                            <div class="info">
                                <?= Alert::widget() ?>
                                <span class="title-block"><?= Html::encode($this->title) ?></span>
                                <div class="info__block">
                                    <?= $content ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content__addit content__addit_gray">
                <?= $this->render('//site/right') ?>
            </section>
            <!-- CONTENT EOF -->
        </div>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>