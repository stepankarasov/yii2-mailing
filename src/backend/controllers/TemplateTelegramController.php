<?php

namespace stepankarasov\mailing\backend\controllers;

use stepankarasov\mailing\common\models\templateTelegram\TemplateTelegram;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Шаблоны Telegram
 */
class TemplateTelegramController extends Controller
{
    /**
     * @param $templateId
     * @return string|\yii\web\Response
     */
    public function actionCreate($templateId)
    {
        $model = new TemplateTelegram();
        $model->templateId = $templateId;

        if ($model->load(Yii::$app->request->post(), null) && $model->save()) {
            return $this->redirect(['/mailing/template/view', 'id' => (string)$model->templateId]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post(), null) && $model->save()) {
            return $this->redirect(['/mailing/template/view', 'id' => (string)$model->templateId]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionCopy($id)
    {
        $model = $this->findModel($id);
        $newModel = new TemplateTelegram();
        $newModel->setAttributes($model->getAttributes());
        $newModel->save();

        return $this->redirect(['/mailing/template/view', 'id' => (string)$model->templateId]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['/mailing/template/view', 'id' => (string)$model->templateId]);
    }


    /**
     * @param $id
     * @return TemplateTelegram|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = TemplateTelegram::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
