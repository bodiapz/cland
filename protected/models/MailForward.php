<?php

/**
 * This is the model class for table "mail_forwarding".
 *
 * The followings are the available columns in table 'mail_forwarding':
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $size
 * @property integer $smtp
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $disabled
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class MailForward extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mail_forwarding';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, email_forward, user_id', 'required'),
			array('user_id, disabled', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>100),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, email, email_forward, created_at, updated_at, disabled', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'email' => 'Email',
			'email_forward' => 'Email for forwarding',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'disabled' => 'Disabled',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('email_forward',$this->email_forward,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('disabled',$this->disabled);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Mailbox the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	protected function beforeSave() {
        $purifier = new CHtmlPurifier;
        $this->email = $purifier->purify($this->email);
		if($this -> isNewRecord) {
			$this -> created_at = date('Y-m-d', time());
		}

		$this -> updated_at = date('Y-m-d', time());

		return true;
	}

	/**
	 * Change the status of the mail_forward
	 */
	public function deleteMailForward($email) {
		$criteria = new CDbCriteria;
		$criteria -> condition = 'email=:email';
		$criteria -> params = array(':email' => $email);

		$mailbox = $this -> find($criteria);
		$mailbox -> disabled = 1;
		$mailbox -> save();
	}

	/**
	 * Find the mail_forward by userid
	 */
	public function findMailForwardByUser($user_id, $email_forward = null, $page = 0) {
		$criteria = new CDbCriteria;
		if($email_forward){
			$criteria -> condition = 'user_id = :id and email_forward = :email_forward and disabled is null';
			$criteria -> params = array(':id' => $user_id, ':email_forward' => $email_forward);

            $return = $this -> findAll($criteria);
		}else{
			$criteria -> condition = 'user_id = :id and disabled is null';
			$criteria -> params = array(':id' => $user_id);

            $return = new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
                'pagination' => array(
                    'pageSize' => 5,
                    'currentPage' => $page,
                )
            ));
		}

        return $return;
	}
}
