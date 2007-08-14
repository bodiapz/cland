<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $address
 * @property integer $term
 * @property float $amount
 * @property float $btc_amount
 * @property integer $user_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $method
 * @property string $received_amount
 */
class Transactions extends CActiveRecord
{

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'transactions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(

			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			 array('id, address, term, amount, btc_amount, user_id, status, created_at, updated_at, method, received_amount', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'id' => 'ID',
            'address' => 'BTC address',
            'term' => 'Package',
            'amount' => 'Amount',
            'btc_amount' => 'BTC amount',
            'user_id' => 'User ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'method' => 'Method',
            'received_amount' => 'Received amount'
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
        $criteria->compare('address',$this->address,true);
        $criteria->compare('term',$this->term,true);
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('btc_amount',$this->btc_amount,true);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('status',$this->status,true);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('updated_at',$this->updated_at,true);
        $criteria->compare('method',$this->method,true);
        $criteria->compare('received_amount',$this->received_amount);

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
	protected function beforeSave() {
		if($this -> isNewRecord) {
			$this -> created_at = date('Y-m-d H:i:s', time());
		}

		$this -> updated_at = date('Y-m-d H:i:s', time());

		return true;
	}
	
	public function findLastTransByUser($userID) {
		$criteria=new CDbCriteria;

		$criteria -> condition = 'user_id = :user';
		$criteria -> params = array(':user' => $userID);

		//$criteria -> addCondition('status = "receive"');
		$criteria -> order = 'created_at desc';

		$criteria -> limit = 1;
		
		$transaction = $this -> find($criteria);

		return $transaction;
	}
}
