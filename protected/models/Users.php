<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property integer $security_pin
 * @property string $security_question
 * @property string $security_answer
 * @property double $wallet_amount
 * @property integer $group_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $last_login
 * @property string $last_password_change
 * @property integer $premium
 * @property integer $disabled
 *
 * The followings are the available model relations:
 * @property LoginHistory[] $loginHistories
 * @property Mailbox[] $mailboxes
 * @property Multifactor[] $multifactors
 * @property Payments[] $payments
 * @property Tickets[] $tickets
 * @property Groups $groups
 */
class Users extends CActiveRecord
{
	public $current_password;
	public $new_password;
	public $confirm_new_password;

	public $confirm_password;

	public $current_security_pin;
	
	public $current_security_answer;
	public $new_security_question;
	public $new_security_answer;
	public $new_security_pin;

	public $verifyCode;

	public $emailsuffix = '@clandestine.se';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, password', 'required'),//first_name, last_name, , security_pin, security_question, security_answer
			array('security_pin, group_id, inviter_id, premium, disabled, auto_payment', 'numerical', 'integerOnly'=>true),
			array('password_reminder, wallet_amount', 'numerical'),
			
            array('first_name, last_name', 'length', 'max'=>100),
            array('email', 'length', 'max'=>200),
            array('password, security_question, security_answer', 'length', 'max'=>255),

            array('last_login, last_password_change, downgrade_date, wallet_amount', 'safe'),
			array('downgrade_date', 'default', 'setOnEmpty' => true, 'value' => null),
						
			//validation on requirement
			array('first_name', 'length', 'min' => 2),
			array('security_pin', 'length', 'min' => 3),
			array('security_answer', 'length', 'min' => 2), 
			array('password', 'length', 'min' => 8),
			
			//rules for register
			array('confirm_password', 'safe'),
			array('confirm_password', 'compare', 'compareAttribute'=>'password', 'on'=>'register'),
			array('email','unique', 'message'=>'This email already exists.', 'on' => 'register'),
			array('verifyCode', 'captcha', 'allowEmpty'=> !extension_loaded('gd'), 'on' => 'register'),

			//rules for changepassword
			array('current_password, new_password, confirm_new_password', 'required', 'on' => 'changepassword'),
			array('current_password, new_password, confirm_new_password', 'length', 'min' => 8),
			array('confirm_new_password', 'compare', 'compareAttribute'=>'new_password', 'on'=>'changepassword'),

			//rules for security info
			//array('current_security_answer', 'required', 'on' => 'security'),
			array('new_security_question', 'length', 'max' => 200, 'on' => 'security'),
			array('new_security_answer', 'length' , 'min' => 2, 'on' => 'security'),
            array('new_security_pin', 'length', 'min' => 3, 'on' => 'security'),

			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			 array('id, inviter_id, first_name, last_name, email, password, security_pin, security_question, security_answer, wallet_amount, group_id, created_at, updated_at, last_login, premium, disabled, invite_token', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'loginHistories' => array(self::HAS_MANY, 'LoginHistory', 'user_id'),
			'mailboxes' => array(self::HAS_MANY, 'Mailbox', 'user_id'),
			'multifactors' => array(self::HAS_MANY, 'Multifactor', 'user_id'),
			'payments' => array(self::HAS_MANY, 'Payments', 'user_id'),
			'tickets' => array(self::HAS_MANY, 'Tickets', 'user_id'),
            'groups' => array(self::BELONGS_TO, 'Groups', 'group_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'id' => 'ID',
            'inviter_id' => 'Inviter ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'password' => 'Password',
            'security_pin' => 'Security Pin',
            'security_question' => 'Security Question',
            'security_answer' => 'Security Answer',
            'wallet_amount' => 'Wallet Amount',
            'group_id' => 'Group',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_login' => 'Last Login',
            'last_password_change' => 'Last Password Change',
            'premium' => 'Premium',
            'disabled' => 'Disabled',
            'confirm_password' => 'Confirm Password',
			'confirm_new_password' => 'Confirm Password',
            'invite_token' => 'Invite token'
        );
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('inviter_id',$this->inviter_id);
        $criteria->compare('first_name',$this->first_name,true);
        $criteria->compare('last_name',$this->last_name,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('password',$this->password,true);
        $criteria->compare('security_pin',$this->security_pin);
        $criteria->compare('security_question',$this->security_question,true);
        $criteria->compare('security_answer',$this->security_answer,true);
        $criteria->compare('wallet_amount',$this->wallet_amount);
        $criteria->compare('group_id',$this->group_id);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('updated_at',$this->updated_at,true);
        $criteria->compare('last_login',$this->last_login,true);
        $criteria->compare('premium',$this->premium);
        $criteria->compare('disabled',$this->disabled);
        $criteria->compare('invite_token',$this->invite_token);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Users the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 *
	 */
	protected function beforeSave()
    {
        $purifier = new CHtmlPurifier;
        $this->first_name = $purifier->purify($this->first_name);
        $this->last_name = $purifier->purify($this->last_name);
        $this->security_question = $purifier->purify($this->security_question);
        $this->security_answer = $purifier->purify($this->security_answer);
        $this->email = $purifier->purify($this->email);
		if($this -> isNewRecord) {
			$this -> created_at = date('Y-m-d', time());
		}

		$this -> updated_at = date('Y-m-d', time());

		return true;
	}

	/**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password)
	{
		return $this->hashPassword($password)==$this->password;
	}

	/**
	 * Generates the password hash.
	 * @param string password
	 * @return string hash
	 */
	public function hashPassword($password)
	{
		return hash('whirlpool',$password);
	}

	/**
	 * Update Last Login
	 * @param string email
	 *
	 */
	public function updateLastLogin($email) {
		$model = $this -> findByAttributes(array('email' => $email));		
		$model -> last_login = date('Y-m-d H:i:s', time());
		$model -> save();
	}

	/**
	 * Find the User using email address
	 * @param string email
	 */
	public function findUserByEmail($email) {
		$criteria = new CDbCriteria;
		$criteria -> condition = 'email=:email and disabled is null';
		$criteria -> params = array(':email' => $email);
		return $this -> find($criteria);
	}
	
	public function findUsersAfterLoginDate($interval = '3 MONTH'){
		$criteria = new CDbCriteria;
		$criteria->condition = 'premium is null AND ifnull(to_days(created_at),0) < to_days(CURRENT_DATE - INTERVAL '.$interval.') AND ifnull(to_days(downgrade_date),0) < to_days(CURRENT_DATE - INTERVAL '.$interval.') AND ifnull(to_days(last_login),0) < to_days(CURRENT_DATE - INTERVAL '.$interval.')';
		$users = Users::model()->findAll($criteria);
		return $users;
	}
	
	public function checkUserLoginDate($user_id, $interval = '2 WEEK'){
		$criteria = new CDbCriteria;
		$criteria->condition = 'id='.$user_id.' AND premium is null AND ifnull(to_days(created_at),0) < to_days(CURRENT_DATE - INTERVAL '.$interval.') AND ifnull(to_days(downgrade_date),0) < to_days(CURRENT_DATE - INTERVAL '.$interval.') AND ifnull(to_days(last_login),0) < to_days(CURRENT_DATE - INTERVAL '.$interval.')';
		$users = Users::model()->findAll($criteria);
		return !empty($users);
	}
	
	public function updateLastLoginFromHistory($user_id){
		$login_date = Yii::app()->db->createCommand()
			->select('MAX(login_date) as login_date')
			->from('login_history')
			->where('user_id=:user_id', array(':user_id'=>$user_id))
			->queryRow();

		if(isset($login_date['login_date']) && $login_date['login_date'] !=''){	
			$model = $this -> findByPk($user_id);
			if($model->last_login < $login_date['login_date']){
				$model -> last_login = $login_date['login_date'];
				$model -> save();
			}
		}
	}
}
