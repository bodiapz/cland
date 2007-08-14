<?php
/*
	Bitcoin Webskin - an open source PHP web interface to bitcoind
	Copyright (c) 2011 14STzHS8qjsDPtqpQgcnwWpTaSHadgEewS
*/

// Communicate with bitcoind via JSON RPC calls:
namespace Email\Bitcoin;
define('USERNAME', 	'bitcoinrpc');
define('PASSWORD', 	'GiSktzddeGxhGPWpdGSiZjTSSvibry4xFP5ws3BX9dMb');
define('SCHEME',	'http');        // http  or https  
define('HOST',     	'127.0.0.1');   
define('PORT',     	'8332');       

define('SERVER_NETWORK', 'Bitcoin');     // Display name of the network

// Windows Localhost Server

define('SERVER_LOCALHOST', 		true);   // is server on localhost? true / false
define('SERVER_LOCALHOST_TYPE', 'windows');  // type:  windows, linux
define('WINDOWS_TASKLIST', 		'C:\Windows\System32\tasklist.exe');

define('SERVER',         		'C:\Program Files\Bitcoin\daemon\bitcoind.exe');  // full pathname to bitcoind executable
define('SERVER_NAME',    		'bitcoind.exe'); // name only of bitcoind executable
define('SERVER_TESTNET',    	true); // use testnet?  true / false
define('SERVER_DATADIR', 		'C:\bitcoin-testnet-datadir');  // location of data dir
define('SERVER_CONF',    		'C:\bitcoin-testnet-datadir\test.conf'); // location of conf file
