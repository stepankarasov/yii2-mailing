<?php

namespace stepankarasov\mailing\common\models\templateTelegram;

use stepankarasov\mailing\common\models\template\Template;

/**
 *
 */
trait TemplateTelegramRelations
{
    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::class, [Template::ATTR_MONGO_ID => TemplateTelegram::ATTR_TEMPLATE_ID]);
    }
}
