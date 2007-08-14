<?php
/*
	Bitcoin Webskin - an open source PHP web interface to bitcoind
	Copyright (c) 2011 14STzHS8qjsDPtqpQgcnwWpTaSHadgEewS
*/
namespace Email\Bitcoin;
require_once 'bitcoin-webskin.php';
class rBit extends BitcoinWebskin {

	function getinfo() {
		return	$this->wallet->getinfo();  
	}

	function getbalance($account, $minc = 1){  
		return $this->wallet->getbalance((string) $account,(int)$minc); 
	}

	function getnewaddress($account = ''){  
		return  $this->wallet->getnewaddress( (string) $account); 
	}
	 

	function setaccount($address = '', $account='' ){
		return $this->wallet->setaccount( (string) $address,	 (string) $account); 
	} 

	function getaccount($address = '' ){
		return $this->wallet->getaccount( (string) $address); 
	}  

	function validateaddress($address = '' ){
		return $this->wallet->validateaddress( (string) $address); 
	}  

	function sendtoaddress($address, $amount, $comment ='', $comment_to=''){ 

				 $response = array( );
				if( $address ) { 

					$valid = $this->validateaddress( $address );
					if( !$valid['isvalid'] ) { 
						$response = $valid ;	
						
					}
					
				} 
				
				if( $address && $amount) {  
					 
					$response = $this->wallet->sendtoaddress(
						(string) $address,					
						(float)  $amount,				
						(string) $comment,					
						(string) $comment_to					
					); 
				}
		return $response;
	}
        
    function getbalancebyaddress($address =''){
        $account = $this->getaccount($address);
        return $this->getbalance($account);
    }
 

 	function listaccounts(){
		return $this->wallet->listaccounts(1); 
	} 
	 
	function listtransactions($account, $from = 0, $to){
                 $transactions = $this->wallet->listtransactions(
					(string) $account,
					  (int) $to,
					  (int) $from
				);
                $tran = array();
                foreach($transactions as $transaction){
                     if(1==1 || isset($transaction['confirmations'])){
                        $tran[] = $transaction;
                     }
                }
		return $tran; 
	} 

	function listtransactioncount($account){
		return	count($this->listtransactions($account, 0, 9999999)); 
	} 
 
 	function  listreceivedbyaccount(){
 		return  $this->wallet->listreceivedbyaccount(
					1,
					false 
				); 
 	}
   function getreceivedbyaddress($address =''){ 
				return	 $this->wallet->getreceivedbyaddress(
					(string) $address,				
					   1			
				); 
  }
} 