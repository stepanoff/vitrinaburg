<?php

class VLinkHelper {

	private static $_domainsList = null;

	/**
	 * Получение списка доменов
	 */
	private static function getDomainsList()
	{
		if (self::$_domainsList == null)
		{
			$domains = array(
				'рф', 'AC', 'AD', 'AE', 'AERO', 'AF', 'AG', 'AI', 'AL', 'AM', 'AN', 'AO', 'AQ', 'AR', 'ARPA', 'AS',
				'ASIA', 'AT', 'AU', 'AW', 'AX', 'AZ', 'BA', 'BB', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BIZ', 'BJ', 'BM',
				'BN', 'BO', 'BR', 'BS', 'BT', 'BV', 'BW', 'BY', 'BZ', 'CA', 'CAT', 'CC', 'CD', 'CF', 'CG', 'CH', 'CI',
				'CK', 'CL', 'CM', 'CN', 'CO', 'COM', 'COOP', 'CR', 'CU', 'CV', 'CX', 'CY', 'CZ', 'DE', 'DJ', 'DK', 'DM',
				'DO', 'DZ', 'EC', 'EDU', 'EE', 'EG', 'ER', 'ES', 'ET', 'EU', 'FI', 'FJ', 'FK', 'FM', 'FO', 'FR', 'GA',
				'GB', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GL', 'GM', 'GN', 'GOV', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU',
				'GW', 'GY', 'HK', 'HM', 'HN', 'HR', 'HT', 'HU', 'ID', 'IE', 'IL', 'IM', 'IN', 'INFO', 'INT', 'IO', 'IQ',
				'IR', 'IS', 'IT', 'JE', 'JM', 'JO', 'JOBS', 'JP', 'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR', 'KW',
				'KY', 'KZ', 'LA', 'LB', 'LC', 'LI', 'LK', 'LR', 'LS', 'LT', 'LU', 'LV', 'LY', 'MA', 'MC', 'MD', 'ME',
				'MG', 'MH', 'MIL', 'MK', 'ML', 'MM', 'MN', 'MO', 'MOBI', 'MP', 'MQ', 'MR', 'MS', 'MT', 'MU', 'MUSEUM',
				'MV', 'MW', 'MX', 'MY', 'MZ', 'NA', 'NAME', 'NC', 'NE', 'NET', 'NF', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR',
				'NU', 'NZ', 'OM', 'ORG', 'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN', 'PR', 'PRO', 'PS', 'PT',
				'PW', 'PY', 'QA', 'RE', 'RO', 'RS', 'RU', 'RW', 'SA', 'SB', 'SC', 'SD', 'SE', 'SG', 'SH', 'SI', 'SJ',
				'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'ST', 'SU', 'SV', 'SY', 'SZ', 'TC', 'TD', 'TEL', 'TF', 'TG', 'TH',
				'TJ', 'TK', 'TL', 'TM', 'TN', 'TO', 'TP', 'TR', 'TRAVEL', 'TT', 'TV', 'TW', 'TZ', 'UA', 'UG', 'UK',
				'US', 'UY', 'UZ', 'VA', 'VC', 'VE', 'VG', 'VI', 'VN', 'VU', 'WF', 'WS', 'XN--0ZWM56D', 'XN--11B5BS3A9AJ6G',
				'XN--3E0B707E', 'XN--45BRJ9C', 'XN--80AKHBYKNJ4F', 'XN--90A3AC', 'XN--9T4B11YI5A', 'XN--CLCHC0EA0B2G2A9GCD',
				'XN--DEBA0AD', 'XN--FIQS8S', 'XN--FIQZ9S', 'XN--FPCRJ9C3D', 'XN--FZC2C9E2C', 'XN--G6W251D', 'XN--GECRJ9C',
				'XN--H2BRJ9C', 'XN--HGBK6AJ7F53BBA', 'XN--HLCJ6AYA9ESC7A', 'XN--J6W193G', 'XN--JXALPDLP', 'XN--KGBECHTV',
				'XN--KPRW13D', 'XN--KPRY57D', 'XN--LGBBAT1AD8J', 'XN--MGBAAM7A8H', 'XN--MGBAYH7GPA', 'XN--MGBBH1A71E',
				'XN--MGBC0A9AZCG', 'XN--MGBERP4A5D4AR', 'XN--O3CW4H', 'XN--OGBPF8FL', 'XN--P1AI', 'XN--PGBS0DH',
				'XN--S9BRJ9C', 'XN--WGBH1C', 'XN--WGBL6A', 'XN--XKC2AL3HYE2A', 'XN--XKC2DL3A5EE0H', 'XN--YFRO4I67O',
				'XN--YGBI2AMMX', 'XN--ZCKZAH', 'XXX', 'YE', 'YT', 'ZA', 'ZM', 'ZW'
			);
			self::$_domainsList = implode('|', $domains);
		}
		return self::$_domainsList;
	}

	/**
	 * Детектировать полный URL со всеми возможными наворотами
	 * @param String $text String for detect URL
	 * @param boolean $isFullDetect FALSE - simple detect URL for backend
	 */
	public static function detectUrl($text, $isFullDetect=true)
	{

		$domainsList = self::getDomainsList();

		$regex = ''

			// Вырезается всякая ерудна "до"
			.($isFullDetect
				? '/(?#word boundary before)(?(?=[a-zа-я\d_])(?<![a-zа-я\d_])|(?<![^a-zа-я\d_]))'
				: '/^')

			// Протокол (http|https|ftp|ftps):\\   или   ~/   или   /
			.($isFullDetect
				? '(?#Protocol)(?<protocol>(?:ht|f)tp(?:s?):\/\/|~\/|\/)?'
				: '(?#Protocol)(?<protocol>http(?:s?):\/\/)')

			// Пользователь и пароль
			.($isFullDetect
				? '(?#Username:Password)(?<credentials>[a-zа-я\d_]+(?::[a-zа-я\d_]+)?@)?'
				: '')

			// Домен или IP
			.'(?<domain>
				(?#Subdomains)(?:[-a-zа-я\d_]+\.)+
				(?#TopLevel Domains)(?:'.$domainsList.')
				|
				(?#IP)(?:(?:25[0-5]|[2][0-4][0-9]|[0-1]?[0-9]?[0-9])(?:\.(?:25[0-5]|[2][0-4][0-9]|[0-1]?[0-9]?[0-9])){3})
			)'

			// Порт 1-5 цифр
			.'(?#Port)(?<port>:\d{1,5})?'

			// Основной путь вида bla/bla/bla или %40%41/%50%51, срабатывают ссылки вида <domain>/bla/bla/bla и <domain>
			.'(?#Directories)(?:(?:(?:\/(?:[-a-zа-я\d_~!$+|.,=]|%[a-f\d]{2})+)+|\/)+)?'		// NOTE: Было: ...|\/)+|\#)?  раньше проверялось на #, но тогда ссылки вида domain#anchor не проходили

			// Параметры запроса ?a=1&b=&c=3
			.'(?#Query)(?:
				(?:\?(?:[-a-zа-я\d_~!$+|.,*:]|%[a-f\d]{2})+=?(?:[-a-zа-я\d_~!$+|.,*:=\/]|%[a-f\d]{2})*)
				(?:&(?:[-a-zа-я\d_~!$+|.,*:]|%[a-f\d]{2})+=?(?:[-a-zа-я\d_~!$+|.,*:=\/]|%[a-f\d]{2})*)*
			)*'

			// Якорь вида #qwe123! или #%40%50
			.'(?#Anchor)(?:\#(?:[-a-zа-я\d_~!$|\/.,*:;=]|%[a-f\d]{2})*)?'

			// Обрезается все в конце справа от <буквы, цифры, _, />
			.'(?#word boundary after)(?(?<=[a-zа-я\d_\/])(?![a-zа-я\d_])|(?![^a-zа-я\d_]|$))'

			// i - Не зависит от регистра
			// m - поиск между переводами строк
			// x - не учитывать неэкранированные пробелы и переносы
			// u - обрабатывать строку в utf-8 (возможны глюки для русских букв, если не указывать модификатор i)
			.'/imxu';

		preg_match_all($regex,  $text, $result, PREG_PATTERN_ORDER);

		return $result;
	}

	public static function redirectParser($href, &$inner=false)
	{
		$pass = false; //None shall pass =)
		$inner=false;

		$parts = self::detectUrl($href);

		if(!empty($parts['domain']))
		{
            $domain = Yii::app()->params['domain'];
            $regexp_domain = str_replace('.','\.', $domain);
            if($parts['domain'][0]==$domain || preg_match('/[-a-zа-я\d_]+\.'.$regexp_domain.'/uim',$parts['domain'][0]))
            {
                if(!$parts['protocol'][0])
                    $href = 'http://'.$href;
                $pass = true;
                $inner = true;
            }
		}

		if(!$pass)
			$href = Yii::app()->params['redirectUrl'] . $href;

		return $href;
	}

}
