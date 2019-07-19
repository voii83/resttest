<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

use app\models\Author;

class BookController extends ActiveController
{
    public $modelClass = 'app\models\Book';

    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::className(),
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create']);
        return $actions;
    }

    public function prepareDataProvider($action)
    {
        $model = new $this->modelClass;
        $author = Yii::$app->request->queryParams;

        if ($result = $model->search($author)) {
            return $result;
        }

        throw new NotFoundHttpException;
    }

    public function actionCreate()
    {
        $model = new $this->modelClass;
        $request = Yii::$app->request->post();

        if (Author::findOne($request['id_author'])) {
            $result = $model->create($request);
            return $result;
        }

        throw new NotFoundHttpException;
    }
}