<?php

use stepankarasov\mailing\common\models\templateTelegram\TemplateTelegram;
use yii\bootstrap4\Html;


/* @var $this yii\web\View */
/* @var $model TemplateTelegram */

$this->title = 'Добавить шаблон';
$this->params['breadcrumbs'][] = ['label' => 'Письма', 'url' => ['/mailing/template/index']];
$this->params['breadcrumbs'][] = [
    'label' => $model->template->name,
    'url'   => ['/mailing/template/view', 'id' => (string)$model->templateId]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tariff-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
