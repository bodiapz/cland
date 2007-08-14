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
class DelUsers extends CActiveRecord
{

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'del_users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('user_id, box_size, user_id, first_name, last_name, email, password, security_pin, security_question, security_answer, wallet_amount, group_id, created_at, updated_at, last_login, premium, disabled, delete_date', 'safe'),
			array('downgrade_date, last_login, last_password_change', 'default', 'setOnEmpty' => true, 'value' => null),

			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			 array('id, box_size, user_id, first_name, last_name, email, password, security_pin, security_question, security_answer, wallet_amount, group_id, created_at, updated_at, last_login, premium, disabled, delete_date', 'safe', 'on'=>'search'),
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
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
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
}
