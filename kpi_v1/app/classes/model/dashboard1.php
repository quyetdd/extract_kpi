<?php
namespace Model;

class Dashboard1 extends \Model {
	//Get active user
	public static function get_active_user($from_day, $to_day) {
		$mongodb = \Mongo_Db::instance();
		$result = $mongodb->command(array(
		    'distinct' => 'kpi',
		    'key' => 'data.character_id',
		    'query' => array('event' => 'KpiLogin', 'data.login_time' => array('$gte'=> $from_day, '$lt'=> $to_day))
		));
		return $result['values'];
	}
	//Get not active user
	public static function get_not_active_user($from_day, $to_day) {
		$mongodb = \Mongo_Db::instance();
		$userlogin=Dashboard1::get_active_user($from_day,$to_day);
		$userregister=Dashboard1::get_register_user('1970-01-01',$to_day);
		$result = $mongodb->command(array(
		    'distinct' => 'kpi',
		    'key' => 'data.character_id',
		    'query' => array('data.character_id' => array('$nin'=> $userlogin, '$in'=> $userregister))
		));
		return $result['values'];
	}
	//Daily register user
	public static function get_register_user($from_day, $to_day) {
		$mongodb = \Mongo_Db::instance();
		$result = $mongodb->command(array(
		    'distinct' => 'kpi',
		    'key' => 'data.character_id',
		    'query' => array('event' => 'KpiUserAdd', 'data.create_time' => array('$gte'=> $from_day, '$lt'=> $to_day))
		));
		return $result['values'];
	}
	//Number user
	public static function get_number_user($event) {
		$mongodb = \Mongo_Db::instance();
		$result = $mongodb->command(array(
		    'distinct' => 'kpi',
		    'key' => 'data.character_id',
		    'query' => array('event' => $event)
		));
		return $result['values'];
	}
	//Get payment user
	public static function get_payment_user($from_day, $to_day) {
		$mongodb = \Mongo_Db::instance();
		$result = $mongodb->command(array(
		    'distinct' => 'kpi',
		    'key' => 'data.character_id',
		    'query' => array('event' => 'KpiWalletSettlement', 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day))
		));
		return $result['values'];
	}
	//Get first payment user
	public static function get_first_payment_user($from_day, $to_day) {
		$mongodb = \Mongo_Db::instance();
		$payment_user=Dashboard1::get_payment_user('1970-01-01',$from_day);
		$result = $mongodb->command(array(
		    'distinct' => 'kpi',
		    'key' => 'data.character_id',
		    'query' => array('event' => 'KpiWalletSettlement', 'data.character_id' => array('$nin'=> $payment_user), 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day))
		));
		return $result['values'];
	}
	//Get payment new user
	public static function get_payment_new_user($from_day, $to_day) {
		$mongodb = \Mongo_Db::instance();
		$register_user=Dashboard1::get_register_user($from_day,$to_day);
		$result = $mongodb->command(array(
		    'distinct' => 'kpi',
		    'key' => 'data.character_id',
		    'query' => array('event' => 'KpiWalletSettlement', 'data.character_id' => array('$in'=> $register_user), 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day))
		));
		return $result['values'];
	}
	//Daily revenue
	// public static function get_revenue($from_day, $to_day) {
	// 	$revenue = 0;
	// 	$mongodb = \Mongo_Db::instance();
	// 	$mongodb->select(array('data.after_paid_diamond', 'data.store_type'))->where(array('event' => 'KpiWalletSettlement', 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day)));
	// 	$store_type=$mongodb->get('kpi');
	// 	for ($i=0; $i < sizeof($store_type) ; $i++) { 
	// 		if($store_type[$i]['data']['store_type']==1){
	// 			$revenue = $revenue + $store_type[$i]['data']['after_paid_diamond'];
	// 		}
	// 		if($store_type[$i]['data']['store_type']==2){
	// 			$db = \Mongo_Db::instance();
	// 			$db->select(array('data.after_paid_diamond', 'data.receipt_data.productId'))->where(array('event' => 'KpiWalletSettlement', 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day)));
	// 			$result=$db->get('kpi');
	// 			for ($i=0; $i <sizeof($result) ; $i++) { 
	// 				if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_01'){
	// 					$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/1;
	// 				}
	// 				if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_02'){
	// 					$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/2;
	// 				}
	// 				if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_03'){
	// 					$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/3;
	// 				}
	// 				if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_04'){
	// 					$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/4;
	// 				}
	// 				if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_05'){
	// 					$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/5;
	// 				}
	// 				if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_06'){
	// 					$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/6;
	// 				}
	// 				if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_07'){
	// 					$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/7;
	// 				}
	// 			}
				
	// 		}
	// 	}
	// 	return round($revenue/100,2);
	// }

	//Daily revenue appstore
	public static function get_revenue_appstore($from_day, $to_day) {
		$revenue = 0;
		$mongodb = \Mongo_Db::instance();
		$mongodb->select(array('data.after_paid_diamond'))->where(array('event' => 'KpiWalletSettlement', 'data.store_type' => 1, 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day)));
		$result=$mongodb->get('kpi');
		for ($i=0; $i < sizeof($result) ; $i++) { 
			$revenue +=$result[$i]['data']['after_paid_diamond'];
		}
		return round($revenue/100,2);
	}
	//Daily revenue google play
	public static function get_revenue_ggplay($from_day, $to_day) {
		$revenue = 0;
		$mongodb = \Mongo_Db::instance();
		$mongodb->select(array('data.after_paid_diamond', 'data.receipt_data.productId'))->where(array('event' => 'KpiWalletSettlement', 'data.store_type' => 2, 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day)));
		$result=$mongodb->get('kpi');
		for ($i=0; $i < sizeof($result) ; $i++) { 
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_01'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/1;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_02'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/2;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_03'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/3;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_04'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/4;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_05'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/5;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_06'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/6;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_07'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/7;
			}
			else {$revenue=$revenue;}
		}
		return round($revenue/100,2);
	}
	//Daily revenue appstore first payment user
	public static function get_revenue_appstore_first_payment($from_day, $to_day) {
		$revenue = 0;
		$mongodb = \Mongo_Db::instance();
		$payment_user=Dashboard1::get_payment_user('1970-01-01',$from_day);
		$mongodb->select(array('data.after_paid_diamond'))->where(array('event' => 'KpiWalletSettlement', 'data.character_id' => array('$nin'=> $payment_user), 'data.store_type' => 1, 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day)));
		$result=$mongodb->get('kpi');
		for ($i=0; $i < sizeof($result) ; $i++) { 
			$revenue +=$result[$i]['data']['after_paid_diamond'];
		}
		return round($revenue/100,2);
	}
	//Daily revenue google play first payment user
	public static function get_revenue_ggplay_first_payment($from_day, $to_day) {
		$revenue = 0;
		$mongodb = \Mongo_Db::instance();
		$payment_user=Dashboard1::get_payment_user('1970-01-01',$from_day);
		$mongodb->select(array('data.after_paid_diamond', 'data.receipt_data.productId'))->where(array('event' => 'KpiWalletSettlement', 'data.character_id' => array('$nin'=> $payment_user), 'data.store_type' => 2, 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day)));
		$result=$mongodb->get('kpi');
		for ($i=0; $i < sizeof($result) ; $i++) { 
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_01'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/1;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_02'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/2;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_03'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/3;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_04'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/4;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_05'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/5;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_06'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/6;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_07'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/7;
			}
			else {$revenue=$revenue;}
		}
		return round($revenue/100,2);
	}
	//Daily revenue appstore new user
	public static function get_revenue_appstore_new_user($from_day, $to_day) {
		$revenue = 0;
		$mongodb = \Mongo_Db::instance();
		$register_user=Dashboard1::get_register_user($from_day,$to_day);
		$mongodb->select(array('data.after_paid_diamond'))->where(array('event' => 'KpiWalletSettlement', 'data.character_id' => array('$in'=> $register_user), 'data.store_type' => 1, 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day)));
		$result=$mongodb->get('kpi');
		for ($i=0; $i < sizeof($result) ; $i++) { 
			$revenue +=$result[$i]['data']['after_paid_diamond'];
		}
		return round($revenue/100,2);
	}
	//Daily revenue ggplay new user
	public static function get_revenue_ggplay_new_user($from_day, $to_day) {
		$revenue = 0;
		$mongodb = \Mongo_Db::instance();
		$register_user=Dashboard1::get_register_user($from_day,$to_day);
		$mongodb->select(array('data.after_paid_diamond', 'data.receipt_data.productId'))->where(array('event' => 'KpiWalletSettlement', 'data.character_id' => array('$in'=> $register_user), 'data.store_type' => 2, 'data.settlement_time' => array('$gte'=> $from_day, '$lt'=> $to_day)));
		$result=$mongodb->get('kpi');
		for ($i=0; $i < sizeof($result) ; $i++) { 
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_01'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/1;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_02'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/2;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_03'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/3;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_04'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/4;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_05'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/5;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_06'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/6;
			}
			if ($result[$i]['data']['receipt_data']['productId']=='aliceorder_payment_07'){
				$revenue = $revenue + $result[$i]['data']['after_paid_diamond']/7;
			}
			else {$revenue=$revenue;}
		}
		return round($revenue/100,2);
	}
	//Daily diamond 
	public static function get_diamond($from_day, $to_day) {
		$diamond = 0;
		$mongodb = \Mongo_Db::instance();
		$register_user=Dashboard1::get_register_user($from_day,$to_day);
		$mongodb->select(array('data.free_diamond_value', 'data.paid_diamond_value'))->where(array('event' => 'KpiDiamondConsumption', 'data.consumption_time' => array('$gte'=> $from_day, '$lt'=> $to_day)));
		$result=$mongodb->get('kpi');
		for ($i=0; $i < sizeof($result) ; $i++) { 
			$diamond = $diamond + $result[$i]['data']['free_diamond_value'] + $result[$i]['data']['paid_diamond_value'];
		}
		return $diamond;
	}
	//Preday
	public static function get_preday($from_day, $days) {
		$preday = date("Y-m-d", strtotime("$from_day - $days day"));
		return $preday;
	}
	//to_day
	public static function get_nextday($from_day, $days) {
		$nextday = date("Y-m-d", strtotime("$from_day + $days day"));
		return $nextday;
	}
	//Get retention
	public static function get_retention($from_day, $days) {
		$userlogin=array();
		$userlogincon=array();
		$revention=array();
		$totaluser=count(Dashboard1::get_register_user('1970-01-01', Dashboard1::get_nextday($from_day, 1)));
		$userlogin[0]=Dashboard1::get_active_user($from_day, Dashboard1::get_nextday($from_day, 1));
		$userlogin[1]=Dashboard1::get_active_user(Dashboard1::get_preday($from_day, 1), $from_day);
		for ($i=2; $i < $days; $i++) { 
			$userlogin[$i]=Dashboard1::get_active_user(Dashboard1::get_preday($from_day, $i), Dashboard1::get_preday($from_day, $i-1));
		}
		$userlogincon[0]=array_intersect($userlogin[0],$userlogin[1]);
		for ($i=1; $i <$days-1 ; $i++) { 
			$userlogincon[$i]=array_intersect($userlogincon[$i-1],$userlogin[$i+1]);
		}
		for ($i=0; $i < $days-1; $i++) { 
			$retention[$i]=round(count($userlogincon[$i])/$totaluser,2);
		}

		return $retention[$days-2];
	}
	//Get retention
	public static function get_not_retention($from_day, $days) {
		$userlogin=array();
		$userlogincon=array();
		$revention=array();
		$totaluser=count(Dashboard1::get_register_user('1970-01-01', Dashboard1::get_nextday($from_day, 1)));
		$usernotlogin[0]=Dashboard1::get_not_active_user($from_day, Dashboard1::get_nextday($from_day, 1));
		$usernotlogin[1]=Dashboard1::get_not_active_user(Dashboard1::get_preday($from_day, 1), $from_day);
		for ($i=2; $i < $days; $i++) { 
			$usernotlogin[$i]=Dashboard1::get_not_active_user(Dashboard1::get_preday($from_day, $i), Dashboard1::get_preday($from_day, $i-1));
		}
		$usernotlogincon[0]=array_intersect($usernotlogin[0],$usernotlogin[1]);
		for ($i=1; $i <$days-1 ; $i++) { 
			$usernotlogincon[$i]=array_intersect($usernotlogincon[$i-1],$usernotlogin[$i+1]);
		}
		for ($i=0; $i < $days-1; $i++) { 
			$notretention[$i]=round(count($usernotlogincon[$i])/$totaluser,2);
		}

		return $notretention[$days-2];
	}
}
