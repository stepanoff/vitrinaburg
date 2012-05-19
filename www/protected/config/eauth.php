<?php
return array(
			'class' => 'ext.gporauth.GporAuth',
			'popup' => true, // Использовать всплывающее окно вместо перенаправления на сайт провайдера
			'services' => array( // Вы можете настроить список провайдеров и переопределить их классы
                /*
				'google' => array(
					'class' => 'GoogleOpenIDService',
				),
                */
                'lj' => array(
                    'class' => 'CustomLjService',
                ),
				'yandex' => array(
					'class' => 'CustomYandexService',
				),
				'twitter' => array(
					// регистрация приложения: https://dev.twitter.com/apps/new
					'class' => 'CustomTwitterService',
					'key' => $params['twitter']['key'],
					'secret' => $params['twitter']['secret'],
				),
                /*
				'google_oauth' => array(
					// регистрация приложения: https://code.google.com/apis/console/
					'class' => 'GoogleOAuthService',
					'client_id' => $params['google_oauth']['client_id'],
					'client_secret' => $params['google_oauth']['client_secret'],
					'title' => 'Google (OAuth)',
				),
                */
				'facebook' => array(
					// регистрация приложения: https://developers.facebook.com/apps/
					'class' => 'CustomFacebookService',
					'client_id' => $params['facebook']['client_id'],
					'client_secret' => $params['facebook']['client_secret'],
				),
				'vkontakte' => array(
					// регистрация приложения: http://vkontakte.ru/editapp?act=create&site=1
					'class' => 'CustomVKontakteService',
                    'client_id' => $params['vkontakte']['client_id'],
                    'client_secret' => $params['vkontakte']['client_secret'],
				),
                /*
				'mailru' => array(
					// регистрация приложения: http://api.mail.ru/sites/my/add
					'class' => 'MailruOAuthService',
                    'client_id' => $params['mailru']['client_id'],
                    'client_secret' => $params['mailru']['client_secret'],
				),
				'moikrug' => array(
					// регистрация приложения: https://oauth.yandex.ru/client/my
					'class' => 'MoikrugOAuthService',
                    'client_id' => $params['moikrug']['client_id'],
                    'client_secret' => $params['moikrug']['client_secret'],
				),
				'odnoklassniki' => array(
					// регистрация приложения: http://www.odnoklassniki.ru/dk?st.cmd=appsInfoMyDevList&st._aid=Apps_Info_MyDev
					'class' => 'OdnoklassnikiOAuthService',
                    'client_id' => $params['odnoklassniki']['client_id'],
                    'client_secret' => $params['odnoklassniki']['client_secret'],
                    'client_public' => $params['odnoklassniki']['client_public'],
					'title' => 'Однокл.',
				),
                */
			),
		);
?>