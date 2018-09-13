<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model promocat\oauth2\models\Client */

$this->title = $model->name;
$this->params['subtitle'] = "View oAuth2";
$this->params['breadcrumbs'][] = ['label' => 'oAuth2 Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">
    <div class="row">
        <div class="col-lg-6">
            <?php \promocat\adminlte\widgets\Box::begin(
                [
                    "type" => \promocat\adminlte\widgets\Box::TYPE_INFO,
                    "header" => $model->name,
                    "icon" => "search",
                    "footer" => join('', [
                        Html::a('Delete', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ])
                    ]),
                ]);

            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'identifier',
                    'name',
                    'redirect_uri',
                    'created_at',
                    'updated_at',
                ],
            ]);


            \promocat\adminlte\widgets\Box::end()

            ?>

        </div>
    </div>
</div>

