<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\linkpreview\controllers;

use Fusonic\OpenGraph\Consumer;
use Fusonic\OpenGraph\Objects\Website;
use humhub\components\Controller;
use humhub\libs\CURLHelper;
use humhub\modules\linkpreview\Events;
use humhub\modules\linkpreview\models\forms\LinkPreviewForm;
use humhub\modules\linkpreview\Module;
use Yii;
use yii\httpclient\Client;


/**
 * Description of IndexController
 *
 * @author luke
 */
class IndexController extends Controller
{
    public function actionFetch()
    {
        if (Yii::$app->user->isGuest) {
            return $this->asJson([]);
        }

        if (version_compare(PHP_VERSION, Events::PHP_MIN_VERSION, '<')) {
            Yii::error("Link preview requires at least " . Events::PHP_MIN_VERSION, 'linkpreview');
            return $this->asJson([]);
        }

        $url = Yii::$app->request->post('url');

        if (!preg_match('#^https?://#', $url)) {
            return $this->asJson(['invalid Url' => $url]);
        }

        $html = $this->fetch($url);
        $html = mb_convert_encoding($html, 'UTF-8');


        $c = new Consumer();
        /** @var Website $object */
        $object = $c->loadHtml($html);

        $result['og:site_name'] = $object->siteName;
        $result['og:title'] = $object->title;
        $result['og:description'] = $object->description;
        $result['og:image'] = [];
        foreach ($object->images as $image) {
            $result['og:image'][] = [
                'og:image:url' => $image->url,
                'og:image:width' => $image->width,
                'og:image:height' => $image->height
            ];
        }
        $result['og:type'] = $object->type;

        $model = new LinkPreviewForm();
        $model->url = $url;
        // Temp class name to run LinkPreviewForm::validate() to display errors after fetching
        $model->class = 'Unknown';
        if (isset($result['og:title'])) {
            $model->title = $result['og:title'];
        }
        if (isset($result['og:description'])) {
            $model->description = $result['og:description'];
        }
        if (isset($result['og:image']['og:image:url'])) {
            $model->image = $result['og:image']['og:image:url'];
        }
        $model->validate();
        $result['errors'] = $model->getErrorSummary(true);

        return $this->asJson([
            'url' => $url,
            'output' => $result
        ]);
    }

    private function fetch($url)
    {
        $output = '';
        try {
            /* @var $linkPreviewModule Module */
            $linkPreviewModule = Yii::$app->getModule('linkpreview');
            $http = new Client(['transport' => 'yii\httpclient\CurlTransport']);
            $response = $http->createRequest()
                ->setUrl($url)
                ->setOptions(CURLHelper::getOptions())
                ->addOptions([
                    CURLOPT_USERAGENT => Yii::$app->name . '/' . Yii::$app->version . '; ' . $linkPreviewModule->getName() . '/' . $linkPreviewModule->getVersion(),
                    CURLOPT_FOLLOWLOCATION => true,
                ])
                ->send();

            /**
             * fixed get body
             */
            $output = (string)$response->getContent();
            $contentType = $response->getHeaders()->get('Content-Type');

            if (!empty($contentType) && strpos($contentType, 'charset=windows') !== false) {
                $output = utf8_encode($output);
            }
        } catch (\yii\httpclient\Exception $ex) {
            Yii::error('Could not connect! ' . $ex->getMessage(), 'linkpreview');
        } catch (\Exception $ex) {
            Yii::error('Could not get HumHub API response! ' . $ex->getMessage(), 'linkpreview');
        }

        return $output;
    }

}
