<?php

namespace stepankarasov\mailing\common\models\telegramSendLog;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 *
 */
class TelegramSendLogSearch extends TelegramSendLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['telegramId', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return array|ActiveDataProvider
     */
    public function search($params)
    {
        $query = TelegramSendLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => [static::ATTR_MONGO_ID => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        return $dataProvider;
    }

    public function searchAdmin($params)
    {
        $query = TelegramSendLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => [static::ATTR_MONGO_ID => SORT_DESC]]
        ]);

        $this->load($params, null);

        if (!$this->validate()) {
            return [];
        }

        $query->andFilterWhere(['like', 'telegramId', $this->telegramId]);

        return $dataProvider;
    }
}
