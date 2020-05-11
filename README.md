# commerceCdek
alpha!
<ul>
  <li>Тестовый логин брать <a href="https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-Протоколобменаданными(v1.5)-TestAccount1.5.Тестовыеучетныезаписииихограничения">тут</a></li>
  <li>Айди тарифов <a href="https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-Протоколобменаданными(v1.5)-Приложение1.Услуги(тарифы)ирежимыдоставкиСДЭК">тут</a></li>
  <li>Поправить в JS <a href="https://github.com/0test/commerceCdek/blob/22a5fa73f7e5bc212aa717b7cb4e8a868d3ea3c0/assets/plugins/commerce-cdek/cdek.js#L38">идентификатор</a> формы, используемой сниппетом Order</li>
  </ul>

## Как сделать валидацию? ##
В сниппете Order есть поле "delivery_method". Плагин автоматически добавляет туда поля  city, street, house и flat -- город, улица, дом и квартира соответственно.
Задаём в сниппете Order свои правила валидации под названием cdekRules:
```
							&cdekRules=`{
							"name":{
								"required":"Введите имя",
								"matches":{
									"params":"\/^[\\pL\\s\\-']++$\/uD",
									"message":"Введите имя правильно"
								}
							},
							"email":{
								"required":"Введите email",
								"email":"Неверная почта"
							},
							"phone":{
								"required":"Введите номер телефона",
								"phone":"Неверный телефон"
							},
							"city": {
								"lengthBetween": {
									"params": [2, 255],
									"message": "Введите город"
								}
							},
							"street": {
								"lengthBetween": {
									"params": [2, 255],
									"message": "Введите улицу"
								}
							},
							"house": {
								"required":"Введите номер дома"
							}							
							}`
```
Делаем сниппет orderSelect (название произвольное) и ставим его в prepare.
Внутри что-то подобное:
```
<?php
if ($FormLister->getField('delivery_method') == 'sdek') {
	$FormLister->config->setConfig(array(
		'rules'=>$FormLister->getCFGDef('cdekRules'),
	));
}
else if( $FormLister->getField('delivery_method') == 'fixed' ){
	$FormLister->config->setConfig(array(
		'rules'=>$FormLister->getCFGDef('fixedRules'),
	));
}
```
Где cdekRules - это и есть название ваших параметров валидации в случае, если юзер выбрал СДЭК.
Для примера добавил fixedRules - это параметры, когда выбрана "Фиксированная доставка". Можно убрать.
