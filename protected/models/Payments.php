<?php

/**
 * This is the model class for table "payments".
 *
 * The followings are the available columns in table 'payments':
 * @property integer $id
 * @property integer $package_id
 * @property integer $user_id
 * @property double $amount
 * @property string $payment_date
 * @property string $next_due_date
 * @property string $created_at
 * @property string $updated_at
 * @property integer $paid
 * @property integer $disabled
 *
 * The followings are the available model relations:
 * @property Packages $package
 * @property Users $user
 */
class Payments extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'payments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('package_id, user_id', 'required'),
			array('package_id, user_id, paid, disabled', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('payment_date, next_due_date, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, package_id, user_id, amount, payment_date, next_due_date, created_at, updated_at, paid, disabled', 'safe', 'on'=>'search'),
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
			'package' => array(self::BELONGS_TO, 'Packages', 'package_id'),
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
			'package_id' => 'Package',
			'user_id' => 'User',
			'amount' => 'Amount',
			'payment_date' => 'Payment Date',
			'next_due_date' => 'Next Due Date',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'paid' => 'Paid',
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

		//$criteria->compare('id',$this->id);
		//$criteria->compare('package_id',$this->package_id);
		$criteria->compare('user_id',$this->user_id);
		//$criteria->compare('amount',$this->amount, true);
		//$criteria->compare('payment_date',$this->payment_date,true);
		//$criteria->compare('next_due_date',$this->next_due_date,true);
		$criteria->compare('created_at',$this->created_at,true);
		//$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('paid',$this->paid);
		$criteria->compare('disabled',$this->disabled);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Payments the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 *
	 */
	 
	public function findLastPayment($userID, $paid = true) {
		$criteria=new CDbCriteria;

		$criteria -> condition = 'user_id = :user';
		$criteria -> params = array(':user' => $userID);

		if($paid){
			$criteria -> addCondition('disabled is null and paid = 1');
		}else{
			$criteria -> addCondition('disabled is null and paid is null');
		}
		$criteria -> order = 'created_at desc';

		$criteria -> limit = 1;
		
		$payment = $this -> find($criteria);

		if($payment) {
			return $payment;
		}else{
			return null;
		}
	}
	
	public function findPaymentsByUser($id, $page=0) 
	{
		$criteria=new CDbCriteria;

		$criteria -> condition = 'user_id = :user AND package_id > 0';
		$criteria -> params = array(':user' => $id);
		$criteria -> order = 'created_at desc';
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
                'pageSize' => 5,
                'currentPage' => $page,
			)
		));
	}
	 
	 
	protected function beforeSave() {
		if($this -> isNewRecord) {
			$this -> created_at = date('Y-m-d H:i:s', time());
		} else {
			$this -> updated_at = date('Y-m-d H:i:s', time());
		}

		return true;
	}
	
	public function findDueDateByUser($userID) {
		$criteria=new CDbCriteria;

		$criteria -> condition = 'user_id = :user';
		$criteria -> params = array(':user' => $userID);

		$criteria -> addCondition('(disabled is null or disabled = 0) and paid = 1');
		$criteria -> order = 'created_at desc';

		$criteria -> limit = 1;
		
		$payment = $this -> find($criteria);

		if($payment) {
			return $payment -> next_due_date;
		}else{
			return null;
		}
	}
}
