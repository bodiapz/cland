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
class Invites extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'invites';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, email_invite, inviter_id', 'required'),
			array('inviter_id, user_id, status', 'numerical', 'integerOnly'=>true),
			array('email, email_invite', 'length', 'max'=>100),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, inviter_id, user_id, email, email_forward, created_at, updated_at, disabled', 'safe', 'on'=>'search'),
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
			'inviter_id' => 'Inviter',
			'user_id' => 'User',
			'email' => 'Email',
			'email_invite' => 'Email for invite',
			'created_at' => 'Send Date',
			'updated_at' => 'Updated At',
			'status' => 'Status',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('email_invite',$this->email_invite,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('status',$this->status);

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
			$this -> created_at = date('Y-m-d H:i:s', time());
		}

		$this -> updated_at = date('Y-m-d H:i:s', time());

		return true;
	}

	/**
	 * Find the invite by userid
	 */
	public function findInvitesByUser($inviter_id, $page = 0) {
		$criteria = new CDbCriteria;
			$criteria -> condition = 'inviter_id = :id';
			$criteria -> params = array(':id' => $inviter_id);

            $return = new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
                'pagination' => array(
                    'pageSize' => 5,
                    'currentPage' => $page,
                )
            ));

        return $return;
	}
}
