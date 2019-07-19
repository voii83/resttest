<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Book extends ActiveRecord
{
    public static function tableName()
    {
        return 'books';
    }

    public function rules()
    {
        return [
            [['year_issue', 'id_author'], 'integer'],
            [['title', 'edition'], 'string'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    public function getAuthor()
    {
        return $this->hasOne(Author::className(), ['id' => 'id_author']);
    }

    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
            'author',
        ]);
    }

    public function search($author)
    {
        if ($author) {
            return Book::find()->where(['id_author' => $author])->all();
        }
        return Book::find()->all();
    }

    public function create($request)
    {
        $this->year_issue = $request['year_issue'];
        $this->id_author = $request['id_author'];
        $this->title = $request['title'];
        $this->edition = $request['edition'];

        return $this->save() ? $this : null;
    }

    public function updateAuthor($id_book, $id_author)
    {
        $book = self::findOne($id_book);
        $book->id_author = $id_author;
        $book->save();

        return $this->save() ? $book : null;
    }
}