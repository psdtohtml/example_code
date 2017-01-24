<?php

namespace app\modules\admin\base;
use dektrium\user\filters\AccessRule;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Base admin controller
 * @package app\modules\admin\base
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Controller extends \app\base\Controller
{

    /** @var string $layout admin default layout */
    public $layout = '../layouts/main.php';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'actions' => [
                            'view',
                            'manage',
                            'create',
                            'update',
                            'delete',
                            'manage-ticket',
                            'duplicate',
                            'manage-detail',
                            'create-time',
                            'update-time',
                            'create-new-order',
                            'manage-order-detail',
                            'cancel-order',
                            'countdown',
                            'edit-order',
                            'update-customer',
                            'confirm',
                            'confirm-order',
                            'manage-calendar',
                            'broadcast-message',
                            'create-tax',
                            'update-tax',
                            'report',
                            'confirmation-mail',
                            'send-confirmation',
                            'send-custom-message',
                            'manage-channel',
                            //widget start
                            'button',
                            'tiers-select',
                            'extras-select',
                            'question-answer',
                            'three-step',
                            'four-step',
                            'five-step',
                            'six-step',
                            'save',
                            //widget end
                        ],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['manage', 'update'],
                        'allow' => true,
                        'roles' => ['manager'],
                    ],
                    [
                        'actions' => [
                            'manage-calendar',
                            'view-guest',
                            //widget start
                            'button',
                            'tiers-select',
                            'extras-select',
                            'question-answer',
                            'three-step',
                            'four-step',
                            'five-step',
                            'six-step',
                            'seven-step',
                            'eight-step',
                            'save'
                            //widget end
                        ],
                        'allow' => true,
                        'roles' => ['?'],
                    ],


                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'               => ['get'],
                    'suggestion'           => ['get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id == 'six-step') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }
}