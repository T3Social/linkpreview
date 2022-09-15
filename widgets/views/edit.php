<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $options [] */
/* @var $model humhub\modules\linkpreview\models\LinkPreview */
/* @var $errors string[] */

humhub\modules\linkpreview\assets\Assets::register($this);
?>

<?= Html::beginTag('div', $options) ?>
        <div class="media">
            <div class="btn-remove">
                <i class="fa fa-times"></i>
            </div>

            <div class="preview-errors alert alert-warning"<?= (empty($errors) ? ' style="display:none"' : ''); ?>>
                <?= Yii::t('LinkpreviewModule.base', 'This link preview will not be saved because of errors:'); ?>
                <ul>
                    <li><?= implode('</li><li>', $errors); ?></li>
                </ul>
            </div>

            <div class="media-left media-image">
                <?php if ($model->isNewRecord) : ?>
                    <div class="image-controls text-center" style="display:none;">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" data-action-click="previous" class="btn btn-default btn-sm btn-prev-thumbnail">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button type="button" data-action-click="next" class="btn btn-default btn-sm btn-next-thumbnail">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="help-block">
                            <?= Yii::t('LinkpreviewModule.base', 'Choose a thumbnail'); ?>
                            <br>(<span class="current">1</span> of <span class="total">1</span>)
                        </div>
                    </div>
                <?php elseif($model->image): ?>
                    <?= Html::img($model->image, ['class' => 'media-object', 'style' => 'width: 80px']) ?>
                <?php endif; ?>
            </div>

            <div class="media-body">
                <h4 class="media-heading">
                    <div class="text preview-title-text"><?= Html::encode($model->title) ?></div>
                    <div class="input">
                        <?= Html::activeTextInput($model, 'title', ['id' => null, 'class' => 'form-control title-input', 'style' => 'display:none']); ?>
                    </div>
                </h4>
                <div class="help-block preview-url-text">
                    <?= Html::encode($model->url) ?>
                </div>
                <div class="preview-description">
                    <div class="text preview-description-text"><?= Html::encode($model->description) ?></div>
                    <div class="input">
                        <?= Html::activeTextarea($model, 'description', ['id' => null, 'class' => 'form-control description-input', 'rows' => '4', 'style' => 'display:none'])?>
                    </div>
                </div>
            </div>
        </div>

    <?= Html::activeHiddenInput($model, 'url', ['id' => null, 'class' => 'form-control url-input']) ?>
    <?= Html::activeHiddenInput($model, 'image', ['id' => null, 'class' => 'form-control image-input']) ?>

<?= Html::endTag('div'); ?>