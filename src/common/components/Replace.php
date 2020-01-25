<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class Replace
 * @package common\components
 */
class Replace extends Component
{
    /**
     * @param array  $data
     * @param array  $links
     * @param string $text
     * @param bool   $ssl
     * @return string $text
     */
    public static function replaceTextLinks($data, $links, $text, $ssl = true)
    {
        $webAppLink = ArrayHelper::getValue($links, 'webApp');
        $singInLink = ArrayHelper::getValue($links, 'signIn');
        $paymentLink = ArrayHelper::getValue($links, 'payment');
        $unsubscribeLink = ArrayHelper::getValue($links, 'unsubscribe');

        $unsubscribeLink .= "?email={$this->user->getEmail()}";

        $scheme = $ssl ? 'https://' : 'http://';

        // Корневая ссылка на app.
        $webAppLink = $scheme . str_replace('{host}', $sourceDomain, $webAppLink);

        // Ссылка на регистрацию
        $singInLink = str_replace('{host}', $webAppLink, $singInLink);
        // Ссылка на оплату
        $paymentLink = str_replace('{host}', $webAppLink, $paymentLink);
        // Ссылка отписаться от рассылок
        $unsubscribeLink = str_replace('{host}', $webAppLink, $unsubscribeLink);

        // Подставляем домен в переданные переменные
        foreach ((array)$data as $key => $value) {
            $data[$key] = str_replace('{host}', $webAppLink, $value);
        }

        // API
        $apiEndpoint = ArrayHelper::getValue($links, 'api');

        // Имя пользователя
        $text = str_replace('{firstName}', $this->user->getFirstName(), $text);

        // Ссылка на проект
        $text = str_replace('{webAppLink}', $webAppLink, $text);

        // Ссылка на авторизацию
        $text = str_replace('{singInLink}', $singInLink, $text);

        // Ссылка на оплату
        $text = str_replace('{paymentLink}', $paymentLink, $text);

        // Почта
        $text = str_replace('{email}', $this->user->getEmail(), $text);

        //@todo добавить токен отписки
        $text = str_replace('{unsubscribeLink}', $unsubscribeLink, $text);

        // Дата регистрации
        $text = str_replace('{signUpAt}', $this->user->getCreatedAt()->toDateTime()->format('d.m.Y'), $text);

        // Дата оплаты
        $text = str_replace('{expiredAt}', $this->user->getExpiredAt()->toDateTime()->format('d.m.Y'), $text);


        // Текущая год
        $text = str_replace('{currentYear}', date('Y'), $text);


        $text = str_replace('src="/images', 'src="' . $apiEndpoint . '/images', $text);
        $text = str_replace('src=\'/images', 'src=\'' . $apiEndpoint . '/images', $text);
        $text = str_replace('url("/images', 'url("' . $apiEndpoint . '/images', $text);
        $text = str_replace('url(\'/images', 'url(\'' . $apiEndpoint . '/images', $text);
        $text = str_replace('url(/images', 'url(' . $apiEndpoint . '/images', $text);

        // Подмена данных в шаблоне из переданных переменных
        foreach ((array)$data as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

    }
}