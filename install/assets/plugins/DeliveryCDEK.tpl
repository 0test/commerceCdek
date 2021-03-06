//<?php
/**
 * Delivery CDEK
 *
 * Плагин для расчёт доставки СДЭК
 *
 * @category		module
 * @author			1px.su
 * @version			1.0
 * @license			http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal		@modx_category Commerce
 * @internal    	@events OnCollectSubtotals,OnRegisterDelivery
 * @internal		@properties &title=Название;string;Доставка СДЭК;&price=Минимальная цена;string;&index_from=Индекс города-отправителя;string;&auth_login=СДЭК логин;string;&auth_pass=СДЭК пароль;string;&sdek_tariffid=СДЭК id тарифа по умолчанию;string;137;
 * @internal    @installset base
 */
/***********************************
* 
*	Доставка СДЭК
*
***********************************/
require MODX_BASE_PATH. 'assets/plugins/commerce-cdek/commerce-cdek.php';
