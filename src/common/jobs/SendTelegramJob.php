<?php

namespace stepankarasov\mailing\common\jobs;

use stepankarasov\mailing\common\interfaces\UserInterface;
use stepankarasov\mailing\common\models\telegramSendLog\TelegramSendLog;
use stepankarasov\mailing\common\models\template\Template;
use stepankarasov\mailing\common\models\templateTelegram\TemplateTelegram;
use MongoDB\BSON\ObjectId;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ForceReply;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Message;
use Yii;
use yii\base\BaseObject;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\queue\JobInterface;
use yii\swiftmailer\Mailer;

/**
 *
 */
class SendTelegramJob extends BaseObject implements JobInterface
{
    /**
     * @var ObjectId
     */
    public $key;

    /**
     * Telegram token Api
     * @var string
     */
    public $telegramTokenApi;

    /**
     * @var BotApi
     */
    public $telegramApi;

    /**
     * @var string
     */
    public $telegramId;

    /**
     * @var array
     */
    public $data;

    /**
     * @var string
     */
    public $logId;

    /**
     * @var UserInterface
     */
    public $user;

    /**
     * Название Telegram отправителя
     *
     * @var string
     */
    public $senderTelegram;

    /**
     * Имя отправителя
     *
     * @var string
     */
    public $senderName;

    /**
     * Массив ссылок
     *
     * @var array
     */
    public $links;

    /**
     * @var string
     */
    public $ourDomain;

    /**
     * @var boolean
     */
    public $ssl;

    /**
     * @var UserInterface
     */
    public $userClass;

    /**
     * Шаблон письма
     *
     * @var string
     */
    public $layout = 'html';

    /**
     * @param \yii\queue\Queue $queue
     * @return bool
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        $this->telegramApi = new BotApi($this->telegramTokenApi);
        $this->user = $this->userClass::findByTelegramId($this->telegramId);

        $template = Template::findByKey($this->key);

        $log = TelegramSendLog::findOne($this->logId);
        $sender = [$this->senderTelegram => $this->senderName];

        try {
            if (!$log) {
                // в телеграм
                throw new ErrorException('Лог не найден');
            }


            if (!$template) {
                throw new ErrorException('Шаблон не найден ' . $this->key);
            }

            if (!$this->user) {
                throw new ErrorException('Пользователь не найден ' . $this->telegramId);
            }


            // Поиск основного партнера по реферальному домену
            // @todo переименовать в affiliate
            $referral = $this->user->getReferralByAffiliateDomain()->one();

            // Домен на который будут перенаправлять все письма
            // Если нет партнера, тогда ставим наш домен
            $sourceDomain = $referral ? $referral->affiliateDomain : $this->ourDomain;

            // Шаблон письма для отправки
            // Ищет по ключу, языку и домену партнера
            $templateTelegram = TemplateTelegram::findByKeyAndLangAndAffiliateDomain($template->_id,
                $this->user->getLanguage(),
                $sourceDomain);

            if (!$templateTelegram) {
                throw new ErrorException('Шаблон не найден ' . $this->key . ':' . $sourceDomain);
            }

            $webAppLink = ArrayHelper::getValue($this->links, 'webApp');
            $singInLink = ArrayHelper::getValue($this->links, 'signIn');
            $paymentLink = ArrayHelper::getValue($this->links, 'payment');
            $unsubscribeLink = ArrayHelper::getValue($this->links, 'unsubscribe');

            $unsubscribeLink .= "?email={$this->user->getEmail()}";

            $scheme = $this->ssl ? 'https://' : 'http://';

            // Корневая ссылка на app.
            $webAppLink = $scheme . str_replace('{host}', $sourceDomain, $webAppLink);

            // Ссылка на регистрацию
            $singInLink = str_replace('{host}', $webAppLink, $singInLink);
            // Ссылка на оплату
            $paymentLink = str_replace('{host}', $webAppLink, $paymentLink);
            // Ссылка отписаться от рассылок
            $unsubscribeLink = str_replace('{host}', $webAppLink, $unsubscribeLink);

            // Подставляем домен в переданные переменные
            foreach ((array)$this->data as $key => $value) {
                $this->data[$key] = str_replace('{host}', $webAppLink, $value);
            }

            // API
            $apiEndpoint = ArrayHelper::getValue($this->links, 'api');

            // Имя пользователя
            $body = str_replace('{firstName}', $this->user->getFirstName(), $templateTelegram->body);

            // Ссылка на проект
            $body = str_replace('{webAppLink}', $webAppLink, $body);

            // Ссылка на авторизацию
            $body = str_replace('{singInLink}', $singInLink, $body);

            // Ссылка на оплату
            $body = str_replace('{paymentLink}', $paymentLink, $body);

            // Почта
            $body = str_replace('{email}', $this->user->getEmail(), $body);

            //@todo добавить токен отписки
            $body = str_replace('{unsubscribeLink}', $unsubscribeLink, $body);

            // Дата регистрации
            $body = str_replace('{signUpAt}', $this->user->getCreatedAt()->toDateTime()->format('d.m.Y'), $body);

            // Дата оплаты
            $body = str_replace('{expiredAt}', $this->user->getExpiredAt()->toDateTime()->format('d.m.Y'), $body);


            // Текущая год
            $body = str_replace('{currentYear}', date('Y'), $body);


            $body = str_replace('src="/images', 'src="' . $apiEndpoint . '/images', $body);
            $body = str_replace('src=\'/images', 'src=\'' . $apiEndpoint . '/images', $body);
            $body = str_replace('url("/images', 'url("' . $apiEndpoint . '/images', $body);
            $body = str_replace('url(\'/images', 'url(\'' . $apiEndpoint . '/images', $body);
            $body = str_replace('url(/images', 'url(' . $apiEndpoint . '/images', $body);

            // Подмена данных в шаблоне из переданных переменных
            foreach ((array)$this->data as $key => $value) {
                $body = str_replace($key, $value, $body);
            }

            $keyboard = ($templateTelegram->keyboard) ? json_decode($templateTelegram->keyboard, true) : false;

            $isSend = $this->sendTelegramMail([
                'telegramId' => $this->telegramId,
                'parse_mode' => 'html',
                'text'       => $body
            ], $keyboard, $templateTelegram->isInlineKeyboard);

            if ($isSend) {
                $log->send();
            } else {
                $log->setError('Ошибка отправки');
            }
        } catch (\Throwable $e) {
            $message = '[Отправитель]: ' . print_r($sender, true);
            $message .= '<br>' . $e->getMessage();

            $message .= '<br>' . $e->getTraceAsString();

            $log->setError($message);

            throw new $e;
        }
    }


    /**
     * Отправка письма в телеграм
     * @param            $params
     * @param array|bool $keyboard
     * @param bool       $isInlineKeyboard
     * @return bool
     */
    private function sendTelegramMail($params, $keyboard = false, $isInlineKeyboard = false)
    {
        if (ArrayHelper::getValue($params, 'telegramId')) {
            //&& $this->user->telegramIsActive
            //&& $this->user->isNotificationTelegram) {

            $replyMarkup = null;

            if (is_array($keyboard)) {
                $replyMarkup = ($isInlineKeyboard)
                    ? new InlineKeyboardMarkup($keyboard)
                    : new ReplyKeyboardMarkup($keyboard);
            }

            if (ArrayHelper::getValue($params, 'telegramPhoto')) {
                $this->telegramApi->sendPhoto(
                    ArrayHelper::getValue($params, 'telegramId'),
                    ArrayHelper::getValue($params, 'telegramPhoto'),
                    ArrayHelper::getValue($params, 'text'),
                    null,
                    $replyMarkup,
                    false,
                    'html'
                );
            } else {
                $this->telegramApi->sendMessage(
                    ArrayHelper::getValue($params, 'telegramId'),
                    ArrayHelper::getValue($params, 'text'),
                    null,
                    false,
                    null,
                    $replyMarkup,
                    false
                );
            }

            return true;
        }

        return false;
    }
}
