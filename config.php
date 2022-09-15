<?php

use humhub\modules\content\widgets\richtext\ProsemirrorRichText;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentAddonActiveRecord;
use humhub\modules\linkpreview\Events;
use humhub\components\Application;

return [
    'id' => 'linkpreview',
    'class' => 'humhub\modules\linkpreview\Module',
    'namespace' => 'humhub\modules\linkpreview',
    'events' => [
        // Save
       ['class' => ProsemirrorRichTextEditor::class, 'event' => ProsemirrorRichTextEditor::EVENT_AFTER_RUN, 'callback' => [Events::class, 'onRichTextEditorFieldCreate']],
        ['class' => ContentActiveRecord::class, 'event' => ContentActiveRecord::EVENT_AFTER_INSERT, 'callback' => [Events::class, 'onAfterContentSave']],
        ['class' => ContentActiveRecord::class, 'event' => ContentActiveRecord::EVENT_AFTER_UPDATE, 'callback' => [Events::class, 'onAfterContentSave']],
        ['class' => ContentAddonActiveRecord::class, 'event' => ContentAddonActiveRecord::EVENT_AFTER_INSERT, 'callback' => [Events::class, 'onAfterContentSave']],
        ['class' => ContentAddonActiveRecord::class, 'event' => ContentAddonActiveRecord::EVENT_AFTER_UPDATE, 'callback' => [Events::class, 'onAfterContentSave']],
        ['class' => Application::class, 'event' => Application::EVENT_BEFORE_REQUEST, 'callback' => [Events::class, 'onBeforeRequest']],
        // Output
       ['class' => ProsemirrorRichText::class, 'event' => ProsemirrorRichText::EVENT_AFTER_OUTPUT, 'callback' => [Events::class, 'onRichTextOutput']],
    ],
];
?>