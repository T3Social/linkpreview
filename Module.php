<?php

namespace humhub\modules\linkpreview;

use humhub\components\Event;
use Yii;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;
use humhub\modules\linkpreview\models\forms\LinkPreviewForm;

class Module extends \humhub\components\Module
{

    /**
     * Following event handler were moved to Event.php but remain here due to update compatibility.
     */

    /**
     * Adds the linkpreview editor to the richtext editor field.
     * @param Event $event
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
     * @param type $event
     */
    public static function onRichTextOutput($event)
    {
        try {
            if ($event->sender->record instanceof \yii\db\ActiveRecord) {
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
     * @param Event $event
     * @return void
     */
    public static function onAfterContentSave($event)
    {
        try {
            if (Yii::$app->request->isConsoleRequest) {
                return;
            }

            if ($event->sender instanceof \humhub\modules\activity\models\Activity) {
                return;
            }

            if (!($event->sender instanceof \yii\db\ActiveRecord)) {
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
