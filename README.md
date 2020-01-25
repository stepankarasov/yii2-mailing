Yii2 Mailing extension
======================
Yii2 Mailing extension

Установка
------------------
* Установка пакета с помощью Composer.
В composer.json проекта добавить:
```
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/stepankarasov/yii2-mailing/"
    }
]
```
Затем подключить:
```
composer require stepankarasov/yii2-mailing:dev-master --prefer-source
```

Использование
------------------
* Пример конфигурации
```
'modules'        => [
        'mailing' => [
            'class' => MailingModule::class,
            'senderEmail' => 'hello@stepankarasov.ru',
            'senderName' => 'Test',
            'userClass'    => User::class,
            'links' => [
                'host' => 'http://mailing.loc',
                'api' => 'http://api.mailing.loc',
                'login' => 'http://mailing.loc/auth/login',
                'payment' => 'http://mailing.loc/payment',
                'unsubscribe' => 'http://mailing.loc/auth/unsubscribe',
            ],
        ],
    ],
```
