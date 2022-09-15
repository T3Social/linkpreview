<?php

namespace humhub\modules\linkpreview\models\forms;
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

/**
 * Description of LinkPreviewForm
 *
 * @author buddha
 */
class LinkPreviewForm extends \humhub\modules\linkpreview\models\LinkPreview
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['title'], 'required'],
        ]);
    }

    /**
     * Loads the given request data into the form.
     * Returns true if the data could be loaded and the options were set successfully.
     * 
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        return parent::load($data, $formName) && $this->validate();
    }
    
}
