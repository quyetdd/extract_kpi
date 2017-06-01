<?php
use \Model\Dashboard1;

class Controller_Dashboard1 extends Controller {
	public function action_index() {
		$data=array();
		$from_day='2016-02-07';
		$to_day='2016-02-08';
		$days=3;
		$nextmonth='2016-03-01';
		$starttime='1970-01-01';
		$data['dau']=count(Dashboard1::get_active_user($from_day, $to_day));//Daily active user
		$data['mau']=count(Dashboard1::get_active_user('2016-02-01', $nextmonth));//Monthly active user
		$data['dru']=count(Dashboard1::get_register_user($from_day, $to_day));//Daily register user
		$data['nru']=count(Dashboard1::get_number_user('KpiUserAdd'));//Number register user
		$data['nau']=$data['dau']-$data['dru'];//Number Active User = Daily active user - Daily register user
		$data['npu']=count(Dashboard1::get_number_user('KpiWalletSettlement'));//Number payment user
		$data['nnpu']=$data['nru']-$data['npu'];//Number user not payment
		$data['dpu']=count(Dashboard1::get_payment_user($from_day, $to_day));//Daily payment user
		if($data['dau']==0){
			$data['pdpu']=0;
		}
		else{
			$data['pdpu']=round($data['dpu']/$data['dau']*100,2);//Percent daily payment user
		}
		$data['dfpu']=count(Dashboard1::get_first_payment_user($from_day, $to_day));//Daily first payment user
		if($data['dpu']==0){
			$data['pdfpu']=0;
		}
		else{
			$data['pdfpu']=round($data['dfpu']/$data['dpu']*100,2);//Percent daily first payment user
		}
		$data['dpnu']=count(Dashboard1::get_payment_new_user($from_day, $to_day));//Daily payment new user
		if($data['dru']==0){
			$data['pdpnu']=0;
		}
		else{
			$data['pdpnu']=round($data['dpnu']/$data['dru']*100,2);//Percent daily payment new user
		}
		$data['daily_revenue']=Dashboard1::get_revenue_appstore($from_day, $to_day)+Dashboard1::get_revenue_ggplay($from_day, $to_day);
		$data['revenue']=Dashboard1::get_revenue_appstore($starttime, $to_day)+Dashboard1::get_revenue_ggplay($starttime, $to_day);
		if(count(Dashboard1::get_register_user($starttime,$to_day))==0){
			$data['daily_arpu']=0;
		}
		else{
			$data['daily_arpu']=round($data['daily_revenue']/count(Dashboard1::get_register_user($starttime,$to_day)),2);
		}
		if(count(Dashboard1::get_payment_user($from_day,$to_day))==0){
			$data['daily_arppu']=0;
		}
		else{
			$data['daily_arppu']=round($data['daily_revenue']/count(Dashboard1::get_payment_user($from_day,$to_day)),2);
		}
		$data['revenue_first_payment']=Dashboard1::get_revenue_appstore_first_payment($from_day, $to_day)+Dashboard1::get_revenue_ggplay_first_payment($from_day, $to_day);
		if($data['dfpu']==0){
			$data['first_payment_arppu']=0;
		}
		else{
			$data['first_payment_arppu']=round($data['revenue_first_payment']/$data['dfpu'],2);
		}
		$data['revenue_new_user']=Dashboard1::get_revenue_appstore_new_user($from_day, $to_day)+Dashboard1::get_revenue_ggplay_new_user($from_day, $to_day);
		if($data['dpnu']==0){
			$data['new_user_arppu']=0;
		}
		else{
			$data['new_user_arppu']=round($data['revenue_new_user']/$data['dpnu'],2);
		}
		$data['daily_diamond']=Dashboard1::get_diamond($from_day, $to_day);
		$data['retention']=Dashboard1::get_retention($from_day, $days);
		$data['not_retention']=Dashboard1::get_not_retention($from_day, $days);

		return View::forge('dashboard1/index', $data);
	}
}