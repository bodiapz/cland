<?php
/*
	Bitcoin Webskin - an open source PHP web interface to bitcoind
	Copyright (c) 2011 14STzHS8qjsDPtqpQgcnwWpTaSHadgEewS
*/

require_once 'bitcoin-interface.php';
require_once 'jsonRPCClient.php';

class jsonRPCClientControler implements Bitcoin, Namecoin {

	// Control
	public function start($debug=false) {  
		try { 
			$this->tube = new jsonRPCClient( 
				SCHEME . '://' . USERNAME . ':' . PASSWORD . '@' . HOST . ':' . PORT . '/', $debug
			);
			$this->info = $this->tube->getinfo();  
			return true;
		} catch( Exception $e ) {
			$this->info['error'] = 'start() Error: ' . $e->getMessage();
			return false;
		}	
	}
	
	public function getprocess() {  // get info on server process
		return 'getprocess() not implemented';
	}
	public function kill() {  // kill the local server process
		return 'kill() not implemented';
	}
	
	// Accounts
	public function listaccounts( $minconf=1 ) { 
		try { 
			return $this->tube->listaccounts( (int)$minconf );
		} catch( Exception $e ) {
			return 'listaccounts() Error: ' . $e->getMessage();
		}
	} // end listaccounts
	
    public function listreceivedbyaccount( $minconf=1, $includeempty=false ) { 
		try { 
			return $this->tube->listreceivedbyaccount( (int)$minconf, (bool)$includeempty);
		} catch( Exception $e ) {
			return 'listreceivedbyaccount() Error: ' . $e->getMessage();
		}	
	} // end listreceivedbyaccount
	
	public function getaccountaddress( $account ) { 
		try { 
			return $this->tube->getaccountaddress( (string)$account );
		} catch( Exception $e ) {
			return 'getaccountaddress() Error: ' . $e->getMessage();
		}	
	}

	public function getaddressesbyaccount( $account ) { 
		try { 
			return $this->tube->getaddressesbyaccount( (string)$account );
		} catch( Exception $e ) {
			return 'getaddressesbyaccount() Error: ' . $e->getMessage();
		}
	}

	public function getreceivedbyaccount( $account, $minconf=1 ) { 
		try { 
			return $this->tube->getreceivedbyaccount( (string)$account, (int)$minconf );
		} catch( Exception $e ) {
			return 'getreceivedbyaccount() Error: ' . $e->getMessage();
		}
	}

	public function getbalance( $account, $minconf=1 ) { 	
		try { 
			return $this->tube->getbalance( (string)$account, (int)$minconf );
		} catch( Exception $e ) {
			return 'getbalance() Error: ' . $e->getMessage();
		}
	}
	
	// Transactions
	public function listtransactions( $account, $count=10, $from=0 ) {
		try { 
			return $this->tube->listtransactions( (string)$account, (int)$count, (int)$from );
		} catch( Exception $e ) {
			return 'listtransactions() Error: ' . $e->getMessage();
		}
	} 
	
	public function gettransaction( $txid ) { 
		try { 
			return $this->tube->gettransaction( (string)$txid );
		} catch( Exception $e ) {
			return 'gettransaction Error: ' . $e->getMessage();
		}
	} 
	
	// Addresses
	public function listreceivedbyaddress( $minconf=1, $includeempty=false ) { 
		try { 
			return $this->tube->listreceivedbyaddress( (int)$minconf, (bool)$includeempty );
		} catch( Exception $e ) {
			return 'listreceivedbyaddress() Error: ' . $e->getMessage();
		}
	}

	public function getnewaddress( $account='' ) { 
 		try { 
			return $this->tube->getnewaddress( (string)$account );
		} catch( Exception $e ) {
			return 'getnewaddress() Error: ' . $e->getMessage();
		}
	}

	public function getreceivedbyaddress( $address, $minconf=1 ) { 
 		try { 
			return $this->tube->getreceivedbyaddress( (string)$address, (int)$minconf );
		} catch( Exception $e ) {
			return 'getreceivedbyaddress() Error: ' . $e->getMessage();
		}
	}

	public function getaccount( $address ) { 
		try { 
			return $this->tube->getaccount( (string)$address );
		} catch( Exception $e ) {
			return 'getaccount() Error: ' . $e->getMessage();
		}
	}

	public function setaccount( $address, $account ) { 
		try { 
			return $this->tube->setaccount( (string)$address, (string)$account );
		} catch( Exception $e ) {
			return 'setaccount() Error: ' . $e->getMessage();
		}
	}

	public function validateaddress( $address ) { 	
 		try { 
			return $this->tube->validateaddress( (string)$address );
		} catch( Exception $e ) {
			return 'validateaddress() Error: ' . $e->getMessage();
		}
	}
	
	// Sending
    public function sendtoaddress( $address, $amount, $comment='', $comment_to='' ) { 	
		try { 
			return $this->tube->sendtoaddress( (string)$address, (float)$amount, (string)$comment, (string)$comment_to );
		} catch( Exception $e ) {
			return 'sendtoaddress() Error: ' . $e->getMessage();
		}
	}

	public function sendfrom( $fromaccount, $toaddress, $amount, $minconf=1, $comment='', $comment_to='' ) { 
		try { 
			return $this->tube->sendfrom( 
				(string)$fromaccount,
				(string)$toaddress,
				(float)$amount, 
				(int)$minconf,
				(string)$comment, 
				(string)$comment_to
			);			
		} catch( Exception $e ) {
			return 'sendfrom() Error: ' . $e->getMessage();
		}
	}

	public function sendmany( $fromaccount, $tomany, $minconf=1, $comment='') { 
 		try { 
			return $this->tube->sendmany(   
				(string)$fromaccount,
				(string)$tomany,
				(int)$minconf,
				(string)$comment
			);			
		} catch( Exception $e ) {
			return 'sendmany() Error: ' . $e->getMessage();
		}
	}
	
	public function move( $fromaccount, $toaccount, $amount, $minconf=1, $comment='' ) { 
 		try { 
			return $this->tube->move( 
				(string)$fromaccount,
				(string)$toaccount,
				(float)$amount,			
				(int)$minconf,
				(string)$comment
			);			
		} catch( Exception $e ) {
			return 'move() Error: ' . $e->getMessage();
		}
	}

        public function sendescrow( $escrowaddrs, $amount, $comment='', $comment_to='' ) { 
 		try { 
			return $this->tube->sendescrow( 
				(string)$escrowaddrs,
				(float)$amount,
				(string)$comment,							
				(string)$comment_to
			);			
		} catch( Exception $e ) {
			return 'sendescrow() Error: ' . $e->getMessage();
		}
	}

        public function redeemescrow( $inputtx, $address, $txhex='') { 
 		try { 
			return $this->tube->redeemescrow( 
				(string)$inputtx,
				(string)$address,
				(string)$txhex							
			);			
		} catch( Exception $e ) {
			return 'redeemescrow() Error: ' . $e->getMessage();
		}
	}
	

    // Server

    public function getinfo() { 
		try { 
			return $this->tube->getinfo(); 
		} catch( Exception $e ) {
			return 'getinfo() Error: ' . $e->getMessage();
		}	
	}

	public function getblockcount() { 
		try { 
			return $this->tube->getblockcount(); 
		} catch( Exception $e ) {
			return 'getblockcount() Error: ' . $e->getMessage();
		}	
	}

	public function getblocknumber() { 
		try { 
			return $this->tube->getblocknumber(); 
		} catch( Exception $e ) {
			return 'getblocknumber() Error: ' . $e->getMessage();
		}
	}

	public function getconnectioncount() { 
		try { 
			return $this->tube->getconnectioncount(); 	
		} catch( Exception $e ) {
			return 'getconnectioncount() Error: ' . $e->getMessage();
		}
	}

	public function getdifficulty() { 
		try { 
			return $this->tube->getdifficulty(); 
		} catch( Exception $e ) {
			return 'getdifficulty() Error: ' . $e->getMessage();
		}
	}

	public function getgenerate() { 
		try { 
			return $this->tube->getgenerate(); 
		} catch( Exception $e ) {
			return 'getgenerate() Error: ' . $e->getMessage();
		}	
	}

	public function gethashespersec() { 
		try { 
			return $this->tube->gethashespersec(); 
		} catch( Exception $e ) {
			return 'gethashespersec() Error: ' . $e->getMessage();
		}	
	}

	public function getwork( $data='' ) { 
		try { 
			return $this->tube->getwork( $data ); 
		} catch( Exception $e ) {
			return 'getwork() Error: ' . $e->getMessage();
		}	
	}

	public function backupwallet( $destination ) { 
		try { 
			return $this->tube->backupwallet( (string)$destination ); 
		} catch( Exception $e ) {
			return 'backupwallet() Error: ' . $e->getMessage();
		}	
	}	

	public function setgenerate( $generate, $genproclimit=-1 ) { 
		try { 
			return $this->tube->setgenerate( (bool)$generate, (int)$genproclimit ); 
		} catch( Exception $e ) {
			return 'setgenerate() Error: ' . $e->getMessage();
		}	
	}

	public function help( $command='' ) { 
		try { 
			return htmlentities( $this->tube->help( $command ) ); 
		} catch( Exception $e ) {
			return 'help() Error: ' . $e->getMessage();
		}	
	}
	
	public function stop() { 
		try { 
			return $this->tube->stop(); 
		} catch( Exception $e ) {
			return 'stop() Error: ' . $e->getMessage();
		}	
	}

	// Namecoin
	public function name_list( $name ) { 
		try { 
			return $this->tube->name_list( (string)$name);
		} catch( Exception $e ) {
			return 'name_list() Error: ' . $e->getMessage();
		}	
	}

	public function name_scan( $start_name='', $max_returned ) { 
		try { 
			return $this->tube->name_scan( (string)$start_name, (int)$max_returned);
		} catch( Exception $e ) {
			return 'name_scan() Error: ' . $e->getMessage();
		}
	}

	public function name_new( $name ) { 
		try { 
			return $this->tube->name_new( (string)$name);
		} catch( Exception $e ) {
			return 'name_new() Error: ' . $e->getMessage();
		}
	}

	public function name_firstupdate( $name, $rand, $tx, $value ) {
		try { 
			return $this->tube->name_firstupdate( 
				(string)$name,
				(string)$rand,
				(string)$tx,
				(string)$value
			);
		} catch( Exception $e ) {
			return 'name_firstupdate() Error: ' . $e->getMessage();
		}
	}

	public function name_update( $name, $value, $toaddress='' ) { 
		try { 
			return $this->tube->name_update( (string)$name, (string)$value, (string)$toaddress);
		} catch( Exception $e ) {
			return 'name_update() Error: ' . $e->getMessage();
		}
	}

	public function name_clean() { 
		try { 
			return $this->tube->name_clean();
		} catch( Exception $e ) {
			return 'name_clean() Error: ' . $e->getMessage();
		}
	}

	public function deletetransaction( $txid ) { 
		try { 
			return $this->tube->deletetransaction( (string)$txid);
		} catch( Exception $e ) {
			return 'deletetransaction() Error: ' . $e->getMessage();
		}
	}
	
} // end class BitcoinPHP

?>