<?php

namespace stepankarasov\mailing\backend\controllers;

use stepankarasov\mailing\common\models\telegramSendLog\TelegramSendLogSearch;
use Yii;

/**
 * Лог
 */
class TelegramSendLogController extends Controller
{

    /**
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $searchModel = new TelegramSendLogSearch();
        $dataProvider = $searchModel->searchAdmin(Yii::$app->request->get());

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}
