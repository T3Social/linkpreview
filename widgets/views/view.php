<?php

use humhub\modules\linkpreview\models\LinkPreview;
use yii\helpers\Html;

 humhub\modules\linkpreview\assets\Assets::register($this);

 /* @var $linkPreview LinkPreview */
?>

<?= Html::beginTag('div', $options)?>
    <hr style="margin-top:0px">
    <div class="media">
        <div class="media-left media-image">
            <?php if($linkPreview->getImageUrl()): ?>
                <?= Html::a(Html::img($linkPreview->getImageUrl(), ['class' => 'media-object', 'style' => 'width: 80px']),
                    $linkPreview->url, ['target' => '_blank']) ?>
            <?php endif; ?>
        </div>
        <div class="media-body">
            <h4 class="media-heading">
                <?= Html::a(Html::encode($linkPreview->title), $linkPreview->url, ['target' => '_blank']) ?>
            </h4>

            <p class="help-block">
                <?= Html::a(Html::encode($linkPreview->url), $linkPreview->url, ['target' => '_blank']) ?>
            </p>

            <div class="description"><?= Html::encode($linkPreview->description) ?></div>
        </div>
    </div>
<?= Html::endTag('div') ?>