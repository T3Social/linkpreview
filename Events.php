<?php

namespace humhub\modules\linkpreview;

use humhub\components\Event;
use Yii;
use yii\db\ActiveRecord;
use humhub\modules\activity\models\Activity;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;
use humhub\modules\linkpreview\models\forms\LinkPreviewForm;

class Events
{
    const PHP_MIN_VERSION = 7.4;

    /**
     * @inheritdoc
     */
    public static function onBeforeRequest()
    {
        try {
            static::registerAutoloader();
        } catch (\Throwable $e) {
            Yii::error($e);
        }

    }

    /**
     * Register composer autoloader when Reader not found
     */
    public static function registerAutoloader()
    {
        if (version_compare(PHP_VERSION, static::PHP_MIN_VERSION, '<')) {
            return;
        }

        require Yii::getAlias('@linkpreview/vendor/autoload.php');
    }

    /**
     * Adds the linkpreview editor to the richtext editor field.
     */
    public static function onRichTextEditorFieldCreate($event)
    {
        try {
            if (static::validateEditorSender($event)) {
                $event->result .= widgets\Editor::widget([
                    'richtextId' => $event->sender->id,
                    'record' => $event->sender->model
                ]);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    private static function validateEditorSender($event)
    {
        /* @var $richtext ProsemirrorRichTextEditor */
        $richtext = $event->sender;

        if (!$richtext->id || !($richtext instanceof ProsemirrorRichTextEditor)) {
            return false;
        }

        return $richtext->id === 'contentForm_message'
            || strpos($richtext->id, 'newCommentForm_') === 0
            || strpos($richtext->id, 'comment_input_') === 0
            || strpos($richtext->id, 'post_input_') === 0;
    }

    /**
     * Appends the linkpreview to the richtext output.
     * @param Event $event
     */
    public static function onRichTextOutput($event)
    {
        try {
            if ($event->sender->record instanceof ActiveRecord) {
                $event->parameters['output'] .= widgets\Viewer::widget([
                    'record' => $event->sender->record
                ]);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * Saves the linkpreview for a given
     * @param  $event
     */
    public static function onAfterContentSave($event)
    {
        try {
            if (Yii::$app->request->isConsoleRequest) {
                return;
            }

            if ($event->sender instanceof Activity) {
                return;
            }

            if (!($event->sender instanceof ActiveRecord)) {
                return;
            }

            $preview = LinkPreviewForm::findByRecord($event->sender);

            if (!$preview) {
                $preview = new LinkPreviewForm();
                $preview->class = $event->sender->className();
                $preview->pk = $event->sender->getPrimaryKey();
            }

            if ($preview->load(Yii::$app->request->post())) {
                $preview->save();
            } else if (!$preview->isNewRecord) {
                $preview->delete();
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }


}
