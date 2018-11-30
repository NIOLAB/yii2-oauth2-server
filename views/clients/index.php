<?php

use promocat\adminlte\widgets\grid\GridView;
use yii\helpers\Html;

;

/* @var $this yii\web\View */
/* @var $searchModel NIOLAB\oauth2\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'oAuth2 Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">
    <div class="row">


        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <div class="col-lg-12">
            <?php \promocat\adminlte\widgets\Box::begin(
                [
                    "type" => \promocat\adminlte\widgets\Box::TYPE_INFO,
                    "header" => $this->title,
                    "icon" => "user",
                    "footer" => Html::a('Create oAuth2 Client', ['create'], ['class' => 'btn btn-success']),
                ]
            );
            ?>


            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'identifier',
                    'name',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url) {
                                return Html::a(new \rmrevin\yii\fontawesome\component\Icon('search') . ' ' . 'View', $url, ['class' => 'btn btn-xs btn-primary']);
                            },
                        ],
                    ],
                ],
            ]); ?>

            <?php \promocat\adminlte\widgets\Box::end() ?>
        </div>
    </div>
</div>

