<?php

/**
 * This is the model class for table "packages".
 *
 * The followings are the available columns in table 'packages':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property double $cost
 * @property integer $term
 * @property string $created_at
 * @property string $updated_at
 * @property integer $disabled
 *
 * The followings are the available model relations:
 * @property Payments[] $payments
 */
class Packages extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'packages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('term, disabled', 'numerical', 'integerOnly'=>true),
			array('cost', 'numerical'),
			array('name', 'length', 'max'=>100),
			array('description', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, cost, term, created_at, updated_at, disabled', 'safe', 'on'=>'search'),
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
			'payments' => array(self::HAS_MANY, 'Payments', 'package_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'description' => 'Description',
			'cost' => 'Cost',
			'term' => 'Term',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('cost',$this->cost);
		$criteria->compare('term',$this->term);
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
	 * @return Packages the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Find current user packages
	 */
	public function findPackageByUser($user) {
		$criteria = new CDbCriteria;
		$criteria -> condition = 'user_id = :user';
		$criteria -> params = array(':user' => $user);
		$criteria -> order = 'id desc';
		$payment = Payments::model()->find($criteria);

		$package = $this -> findByAttributes(array('id' => $payment -> package_id));

		return $package;
	}
}
