<?php

namespace stepankarasov\mailing\backend\controllers;

use stepankarasov\mailing\common\models\template\Template;
use stepankarasov\mailing\common\models\template\TemplateSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Тарифные планы
 */
class TemplateController extends Controller
{

    /**
     * @return string|Response
     */
    public function actionIndex()
    {
        $searchModel = new TemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Template();

        if ($model->load(Yii::$app->request->post(), null) && $model->save()) {
            return $this->redirect(['/mailing/template/view', 'id' => (string)$model->_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $templateEmailProvider = new ActiveDataProvider([
            'query' => $model->getTemplateEmail()
        ]);

        $templateTelegramProvider = new ActiveDataProvider([
            'query' => $model->getTemplateTelegram()
        ]);

        return $this->render('view', [
            'model'                 => $model,
            'templateEmailProvider' => $templateEmailProvider,
            'templateTelegramProvider' => $templateTelegramProvider,
        ]);
    }


    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post(), null) && $model->save()) {
            return $this->redirect(['/mailing/template/view', 'id' => (string)$model->_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['/mailing/template/view', 'id' => (string)$model->_id]);
    }


    /**
     * @param $id
     * @return Template|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Template::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
