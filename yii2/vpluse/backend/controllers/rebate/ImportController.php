<?php

namespace backend\controllers\rebate;

use Yii;
use backend\controllers\SiteController;
use backend\models\rebate\ImportForm;
use yii\web\UploadedFile;
use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use common\models\rebate\UserRequest;
use common\models\User;
use backend\components\ChangeBalance;

class ImportController extends SiteController
{
    public function behaviors()
    {
        return [
            ChangeBalance::className(),
        ] + parent::behaviors();
    }

    public function actionIndex()
    {
        $model = new ImportForm();

        if (Yii::$app->request->post()) {
            $model->csv = UploadedFile::getInstance($model, 'csv');
            $model->scenario = ImportForm::SCENARIO_UPLOAD;
            if ($model->validate()) {

                $filename = $model->csv->name;
                $path = Yii::getAlias("@webroot") . '/uploads/csv/' . $filename;

                if(file_exists($path)) {
                    Yii::$app->session->setFlash('error', 'Файл уже загружен');

                    return $this->renderView($model);
                }
                if ($model->csv->saveAs($path)) {
                    if(!$csv_data = $this->getCsvData($path)) {
                        Yii::$app->session->setFlash('error', 'Не удалось прочитать содержимое файла');
                        unlink($path);

                        return $this->renderView($model);

                    }
                    $user_requests =  UserRequest::find()->where(['status' => 1])->all();
                    if(!$user_requests) {
                        Yii::$app->session->setFlash('error', 'Не удалось получить заявки пользователей');
                        unlink($path);

                        return $this->renderView($model);

                    }
                    $validation_errors = $this->validateCsvData($csv_data);
                    if($validation_errors) {
                        Yii::$app->session->setFlash('error', 'Неверный формат файла');
                        unlink($path);

                        return $this->renderView($model, null, $validation_errors);
                    }
                    if(!$report = $this->saveData($user_requests, $csv_data)) {
                        unlink($path);
                    }

                    return $this->renderView($model, $report);

                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось загрузить файл');
                }
            }
        }

        return $this->renderView($model);
    }

    private function renderView($model, $report = [], $validation_errors = [])
    {
        return $this->render('index', [
            'model' => $model,
            'report' => $report,
            'validation_errors' => $validation_errors,
        ]);
    }

    private function getCsvData($file)
    {
        $importer = new CSVImporter;
        //Will read CSV file
        $importer->setData(new CSVReader([
            'filename' => $file,
            'fgetcsvOptions' => [
                'delimiter' => ';'
            ]
        ]));

        return $importer->getData();
    }

    private function saveData($user_requests, $csv_data) {

        $matches = []; $report = []; $errors = 0;
        foreach ($user_requests as $num => $request) {
            foreach ($csv_data as $str => $csv) {
                if(!$csv) {
                    continue;
                }
                if(!isset($csv[0]) || !isset($csv[1]) ||  !isset($csv[1]) ) {
                    continue;
                }
                if ($request->companyName != $csv[0] || $request->account != $csv[1]) {
                    continue;
                }
                $matches[] = $str;
                $report[$str]['type'] = 'success';
                $report[$str]['message'] = 'Данные приняты успешно';
                $balance = str_replace(',', '.', $csv[2]) / 100 * 90;
                if(!$this->changeBalance(User::findOne($request->id_user), $balance, $request->account, $request->companyName)) {
                    $report[$str]['type'] = 'error';
                    $report[$str]['message'] = 'Не удалсь сохранить данные';
                    $errors++;
                }
            }
        }
        if (!$matches) {
            Yii::$app->session->setFlash('error', 'Нет ни одного совпаденя');
        } else {
            foreach ($csv_data as $str => $csv) {
                $report[$str]['company'] = $csv[0];
                $report[$str]['account'] = $csv[1];
                $report[$str]['balance'] = $csv[2];
                if (!in_array($str, $matches)) {

                    $report[$str]['type'] = 'error';
                    $report[$str]['message'] = 'Нет совпадения с заявками пользователей.';
                    $errors++;
                }
            }
            if (!$errors) {
                Yii::$app->session->setFlash('success', 'Импорт завершился успешно');
            } else {
                Yii::$app->session->setFlash('error', 'Импорт завершился с ошибками');
            }
        }

        return $report;
    }

    private function validateCsvData($data) {

        $model = new ImportForm();
        $model->scenario = ImportForm::SCENARIO_VALIDATION;
        $validation_errors = [];
        foreach ($data as $str => $csv) {
            if(!$csv) {
                $validation_errors[$str] = 'Строка пуста';
                continue;
            }
            if(!isset($csv[0]) || !isset($csv[1]) ||  !isset($csv[1]) ) {
                $validation_errors[$str] = 'Не все параметры в строке заполнены';
                continue;
            }
            $model->company = $csv[0];
            $model->account = $csv[1];
            $model->balance = $csv[2];

            $model->validate();
            if($model->hasErrors()) {
                $errors = $model->getErrors();
                $validation_errors[$str] = '';
                foreach ($errors as $attribute => $error) {
                    $validation_errors[$str] .= implode(' ', $error). '<br>';
                }
            }
        }

        return $validation_errors;
    }

}
