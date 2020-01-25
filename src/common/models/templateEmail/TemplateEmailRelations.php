<?php

namespace stepankarasov\mailing\common\models\templateEmail;

use stepankarasov\mailing\common\models\template\Template;

/**
 *
 */
trait TemplateEmailRelations
{
    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::class, [Template::ATTR_MONGO_ID => TemplateEmail::ATTR_TEMPLATE_ID]);
    }
}
