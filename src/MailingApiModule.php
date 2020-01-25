<?php

namespace stepankarasov\mailing;

/**
 * Class MailingModule
 * @package mailing
 */
class MailingApiModule extends MailingModule
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $this->controllerNamespace = 'stepankarasov\mailing\frontend\controllers';
        $app->getUrlManager()->addRules(['/mailing/pixel/open/<logId>.png' => '/mailing/pixel/open'], false);
        parent::bootstrap($app);
    }
}
