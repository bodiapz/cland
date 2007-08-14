<?php
//    */1 * * * * /usr/bin/php /var/www/clandestine.se/protected/yiic.php cron bitcoin
class CronCommand extends CConsoleCommand
{
	public function bitcoin($debug=0)
		$bitCoin = new rBit(); 
		
		//$baddress = $bitCoin->getnewaddress();
		//$bitCoin->setaccount($baddress, 'general');

		$accounts = $bitCoin->listaccounts();
		foreach($accounts as $account => $amount){
			if($amount > 0){
				$response = $bitCoin->move($account, 'general', $amount);
				$db_->query('UPDATE fluxbb_users SET wallet_amount = wallet_amount + '.$amount.' WHERE id = '.$account);
				$address = $db_->selectOne('SELECT * FROM fluxbb_bitcoin_addresses WHERE user_id = ' . (int)$account . ' ORDER BY id desc limit 1');
				if(!empty($address)){
					$db_->query('UPDATE fluxbb_bitcoin_addresses SET last_balance = '.$amount.' WHERE id = '.$address['id']);
				}
			}
	}
}