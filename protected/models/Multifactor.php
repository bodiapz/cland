<?php

/**
 * This is the model class for table "multifactor".
 *
 * The followings are the available columns in table 'multifactor':
 * @property integer $id
 * @property integer $user_id
 * @property string $user_key
 * @property integer $next_index
 * @property integer $enabled
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class Multifactor extends CActiveRecord
{
	public $code;
	public $next_code;
	public $login_code;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'multifactor';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, user_key', 'required'),
			array('user_id, next_index, enabled', 'numerical', 'integerOnly'=>true),
			array('user_key', 'length', 'max'=>11),
			//array('user_key', 'unique', 'className' => 'Multifactor'),

			array('code','required','on' => 'enable'),
			array('next_code','required','on' => 'disable'),
			array('login_code','required','on' => 'login'),
			array('next_code, login_code', 'safe'),

			//array('code','required','on' => 'disable'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, user_key, next_index, enabled', 'safe', 'on'=>'search'),
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
			'user_key' => 'Key',
			'next_index' => 'Next Index',
			'enabled' => 'Enabled',
			'code' => 'Code',
			'next_code' => 'Code',
			'login_code' => 'Code'
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
		$criteria->compare('user_key',$this->user_key,true);
		$criteria->compare('next_index',$this->next_index);
		$criteria->compare('enabled',$this->enabled);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Multifactor the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 *
	 */
	public function checkMultiFactorEnabled($id) {
		$criteria = new CDbCriteria;
		$criteria -> condition = 'user_id = :id and enabled is not null';
		$criteria -> params = array(':id' => $id);

		$multifactor = $this -> find($criteria);

		if(empty($multifactor)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 *
	 */
	public function findNextCodeIndexByUser($id) {
		$criteria = new CDbCriteria;
		$criteria -> condition = 'user_id = :id and enabled is not null';
		$criteria -> params = array(':id' => $id);

		$authentication = $this -> find($criteria);

		if($authentication) {
			return $authentication -> next_index;
		}

		return null;
	}
	
	/**
	 * checks if multifactor authentication is enabled for user
	 */
	public function isEnabled($userID) {
		$criteria = new CDbCriteria;
		$criteria -> condition = 'user_id = :user and enabled is not null';
		$criteria -> params = array(':user' => $userID);

		if($this -> find($criteria)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * disable the multi factor authentication
	 *
	 */
	public function disableAuthentication($userID) {
		$criteria = new CDbCriteria;
		$criteria -> condition = 'user_id = :user and enabled is not null';
		$criteria -> params = array(':user' => $userID);

		$authentication = $this -> find($criteria);
		$authentication -> enabled = null;
		$authentication -> save();
	}
}
