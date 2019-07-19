<?php

namespace app\controllers;

use Yii;
use yii\helpers\Html;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

use app\models\Author;
use app\models\Book;

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
        unset($actions['update']);
        unset($actions['delete']);

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
        $request = Yii::$app->request->post();

        if (Author::findOne($request['id_author'])) {
            $model = new $this->modelClass;
            $result = $model->create($request);
            return $result;
        }

        throw new NotFoundHttpException;
    }

    public function actionUpdate()
    {
        $id_book = Yii::$app->request->queryParams['id'];
        $id_author = Yii::$app->request->queryParams['id_author'];

        if (Author::findOne($id_author) && Book::findOne($id_book)) {
            $model = new $this->modelClass;
            $result = $model->updateAuthor($id_book, $id_author);
            return $result;
        }
        throw new NotFoundHttpException;
    }

    public function actionDelete()
    {
        $id_book = Yii::$app->request->queryParams['id'];
        $id_author = Yii::$app->request->queryParams['id_author'];

        if (Author::findOne($id_author)) {
            if ($id_book == 0) {
                Author::deleteAll('id = :id_author', ['id_author' => $id_author]);
                $modelBook = new $this->modelClass;
                $modelBook->deleteByAuthor($id_author);
                return true;
            }
        }
        throw new NotFoundHttpException;
    }
}