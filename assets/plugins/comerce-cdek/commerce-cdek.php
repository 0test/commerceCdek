<?php
$e = $modx->event;
$default_price =  ci()->currency->convertToActive($params['price']);
$index_from = $params['index_from'];
$auth_login = $params['auth_login'];
$auth_pass = $params['auth_pass'];
$sdek_tariffid = $params['sdek_tariffid'];

if (!function_exists('getDeliveryFields')) {
	function getDeliveryFields($data){
		$key = 'delivery_fields';
		if (!isset($_SESSION[$key])) {
			$_SESSION[$key] = [];
		}
		foreach (['city', 'street', 'house', 'flat', 'cdek_zip'] as $field) {
			if (isset($data[$field])) {
				$_SESSION[$key][$field] = $data[$field];
			}
		}
		return $_SESSION[$key];
	}
}
$src = "<script type='text/javascript' src='assets/plugins/commerce-cdek/jquery-ui.js'></script>";
$src .= "<link type='text/css' href='assets/plugins/commerce-cdek/jquery-ui.css'  rel='stylesheet'>";
$src .= "<script type='text/javascript' src='assets/plugins/commerce-cdek/cdek.js'></script>";
$modx->regClientScript($src);

if (empty($params['title'])) {
	$lang = $modx->commerce->getUserLanguage('delivery');
	$params['title'] = $lang['delivery.pickup_title'];
}

switch ($e->name) {
	case 'OnCollectSubtotals': {
		$processor = $modx->commerce->loadProcessor();
		if ($processor->isOrderStarted() && $processor->getCurrentDelivery() == 'sdek') {
			$params['total'] += $price;
			$params['rows']['sdek'] = [
				'title' => $params['title'],
				'price' => $default_price
			];
			$data = getDeliveryFields($_REQUEST);
			if( !empty($data['cdek_zip']) ){
				include_once("cdek.class.php");
				try {
					$calc = new CalculatePriceDeliveryCdek();
					$calc->setAuth($auth_login, $auth_pass);
					//устанавливаем город-отправитель
					$calc->setSenderCityPostCode($index_from);
					//устанавливаем город-получатель
					$calc->setReceiverCityPostCode($data['cdek_zip']);
					//устанавливаем дату планируемой отправки
					$calc->setDateExecute( date("Y-m-d") );
					//устанавливаем тариф по-умолчанию
					$calc->setTariffId( $sdek_tariffid );
					//вес и объём. данных нет, ставим тестовые
					$calc->addGoodsItemByVolume(0.1, 0.001);
					if ($calc->calculate() === true) {
						$res = $calc->getResult();
						$modx->setPlaceholder('cdekMessages', 'Планируемая дата доставки: c ' . date_format( date_create($res['result']['deliveryDateMin']), 'd.m.Y' ) . ' по ' . date_format( date_create($res['result']['deliveryDateMax']), 'd.m.Y' ));
					} else {
						$err = $calc->getError();
						if( isset($err['error']) && !empty($err) ) {
							$modx->setPlaceholder('cdekMessages', "v1 " . $err['error'][0]['text'] );
						}
					}
				} catch (Exception $e) {
					$modx->setPlaceholder('cdekMessages', "v2 " . $e->getMessage() );
				}
				
				 //Фикс. сумма. Если нет, то $params['price']
				if( $res['result']['price'] < $params['price']){
					$res['result']['price'] = $params['price'];
				}
				$new_price =  ci()->currency->convertToActive( $res['result']['price'] );
				$params['rows']['sdek']['price'] = $new_price;
			}
		}
		break;
	}

	case 'OnRegisterDelivery': {
		$processor = $modx->commerce->loadProcessor();
		$params['rows']['sdek'] = [
			'title' => $params['title'],
			'price' => $default_price
		];
		if ($processor->isOrderStarted() && $processor->getCurrentDelivery() == 'sdek') {
			$data = getDeliveryFields($_REQUEST);
			$markup = '
					<div class="elBlock cart__item">
						<div class="inputIcon required ui-widget">
							<input type="text" id="cdek_city" class="form-control input required " placeholder="Город" name="city" value="' . (!empty($data['city']) ? htmlentities($data['city']) : '') . '">
							<div class="inputIcon__item "></div>
							<label for="receiver" class="inputIcon__icon city"></label>
							[+city.error+]
						</div>
						<div class="inputIcon required">
							<input type="text" class="form-control input required" placeholder="Улица" name="street" value="' . (!empty($data['street']) ? htmlentities($data['street']) : '') . '">
							<div class="inputIcon__item "></div>
							<label for="receiver" class="inputIcon__icon street"></label>
							[+street.error+]
						</div>
						<div class="inputIcon required">
							<input type="text" class="form-control input required" placeholder="Дом" name="house" value="' . (!empty($data['house']) ? htmlentities($data['house']) : '') . '">
							<div class="inputIcon__item "></div>
							<label for="receiver" class="inputIcon__icon house"></label>
							[+house.error+]
						</div>
						<div class="inputIcon required">
							<input type="text" class="form-control input required" placeholder="Кв" name="flat" value="' . (!empty($data['flat']) ? htmlentities($data['flat']) : '') . '">
							<div class="inputIcon__item "></div>
							<label for="receiver" class="inputIcon__icon flat"></label>
							[+flat.error+]
						</div>
					</div>
					<div class="elBlock cart__item">
						[+cdekMessages+]
						<input type="hidden" name="cdek_zip" id="cdek_zip" value="' . (!empty($data['cdek_zip']) ? htmlentities($data['cdek_zip']) : '') . '">
					</div>
			';

			$params['rows']['sdek']['markup'] = $markup;
		}
		break;
	}
}
$modx->event->output($out);