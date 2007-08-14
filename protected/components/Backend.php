<?php
/** 
 * Class for backend functions
 *
 * @author _uJJwAL_
 * @copyright 2014
 *
 *
 */
class Backend {
	/**
	 * Call the backend script to create the mailbox
	 *
	 */
	public function createMailBox() {
		/* command to query script on backend to check the mailbox usage */
		$cmd = 'ssh -i /var/www/.ssh/mail_php.key frontend@192.168.1.2 php /var/frontend/scripts/mailbox/create.php';
		
		/* execute the command */
		$output = shell_exec($cmd);

		//var_dump($output);
	}

	/**
	 * Call the backend script to change the password
	 */
	public function updateMailBox() {
		/* command to query script on backend to check the mailbox usage */
		$cmd = 'ssh -i /var/www/.ssh/mail_php.key frontend@192.168.1.2 php /var/frontend/scripts/mailbox/change.php';
		
		/* execute the command */
		$output = shell_exec($cmd);

		//var_dump($output);
	}

	/**
	 * SSh Connect to get the mailbox usage
	 * @param string MailAddress
	 * @return Array Quota/Usage
	 */
	public function getMailBoxUsage($mailAddress) {
		/* command to query script on backend to check the mailbox usage */
		$cmd = 'ssh -i /var/www/.ssh/mail_php.key frontend@192.168.1.2 php /var/frontend/scripts/mailbox/usage.php '. $mailAddress;
		
		/* execute the command */
		$output = shell_exec($cmd);

		/* output format - admin@clandestine.se:x:8:0:200.00:0.22:admin@clandestine.se */
		@list($email, $the_letter_x, $user_id_number, $client_id_number, $quota, $used) = explode(':', $output);
		$return['quota'] = $quota;
		$return['used'] = $used;
		
		return $return;
	}

	/**
	 * SSh Connect to delete mailbox from dbmail
	 * @param string $email
	 *
	 */
	public function deleteAllMail($email) {
		/* command to query script on backend to check the mailbox usage */
		$cmd = 'ssh -i /var/www/.ssh/mail_php.key frontend@192.168.1.2 php /var/frontend/scripts/mailbox/delete.php '. $email;
		
		/* execute the command */
		$output = shell_exec($cmd);
	}
	
	/**
	 * SSh Connect to delete mail from mailbox
	 * @param string $email
	 *
	 */
	public function emptyAllMail($email) {
		/* command to query script on backend to check the mailbox usage */
		$cmd = 'ssh -i /var/www/.ssh/mail_php.key frontend@192.168.1.2 php /var/frontend/scripts/mailbox/empty.php '. $email;
		
		/* execute the command */
		$output = shell_exec($cmd);
	}
	
	/**
	 * SSh Connect to add forward mailbox
	 * @param string $email, $email_forward
	 *
	 */
	public function addForwardMail($email, $email_forward) {
		/* command to query script on backend to check the mailbox usage */
		$cmd = 'ssh -i /var/www/.ssh/mail_php.key frontend@192.168.1.2 php /var/frontend/scripts/mailbox/forward_add.php '. $email . ' '. $email_forward;
		
		/* execute the command */
		$output = shell_exec($cmd);
	}
	
	/**
	 * SSh Connect to delete forward mailbox
	 * @param string $email, $email_forward
	 *
	 */
	public function deleteForwardMail($email, $email_forward) {
		/* command to query script on backend to check the mailbox usage */
		$cmd = 'ssh -i /var/www/.ssh/mail_php.key frontend@192.168.1.2 php /var/frontend/scripts/mailbox/forward_del.php '. $email . ' '. $email_forward;
		
		/* execute the command */
		$output = shell_exec($cmd);
	}
}