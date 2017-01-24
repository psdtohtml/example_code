<?php
use frontend\assets\VpluseAsset;
use yii\helpers\Html;
use common\widgets\Alert;
use frontend\widgets\ServiceMenuWidget;

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
            <?= $this->render('//vpluse/head') ?>
            <!-- HEADER EOF -->

            <!-- BEGIN SIDEBAR -->
            <?php
            $service = isset($_SESSION['service']) ? $_SESSION['service'] : null;
            switch ($service) {
                case 'rebate' :
                    echo $this->render('//vpluse/rebate_left');
                    break;
                case 'vps' :
                    echo $this->render('//vpluse/vps_left');
                    break;
                case 'bot' :
                    echo $this->render('//vpluse/bot_left');
                    break;
                default : echo $this->render('//vpluse/partner_left');
            }
            ?>
            <!-- SIDEBAR EOF -->

            <!-- BEGIN CONTENT -->
            <section class="content">
                <div class="block__container">
                    <div class="content__main">
                        <nav class="nav-block">
                            <?= ServiceMenuWidget::widget(); ?>
                        </nav>
                        <?= Alert::widget() ?>
                        <?= $content ?>
                    </div>
                </div>
            </section>
            <section class="content__addit content__addit_gray">
                <?= $this->render('//vpluse/right') ?>
            </section>
            <!-- CONTENT EOF -->
        </div>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>