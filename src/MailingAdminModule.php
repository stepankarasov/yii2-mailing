<?php

namespace stepankarasov\mailing;

/**
 * Class MailingModule
 * @package mailing
 */
class MailingAdminModule extends MailingModule
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $this->controllerNamespace = 'stepankarasov\mailing\backend\controllers';
        $this->setViewPath($this->getBasePath() . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'views');

        parent::bootstrap($app);
    }
}
