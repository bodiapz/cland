<?php

/**
 * This is the model class for table "temp_mailbox".
 *
 * The followings are the available columns in table 'temp_mailbox':
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $size
 * @property integer $smtp
 * @property integer $updated
 */
class TempMailbox extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'temp_mailbox';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email', 'required'),
			array('smtp, user_id, updated', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>200),
			array('password', 'length', 'max'=>255),
			array('size', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, email, password, size, smtp, updated', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
            'user_id' => 'User ID',
			'email' => 'Email',
			'password' => 'Password',
			'size' => 'Size',
			'smtp' => 'Smtp',
			'updated' => 'Updated',
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
		$criteria->compare('password',$this->password,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('smtp',$this->smtp);
		$criteria->compare('updated',$this->updated);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TempMailbox the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Find the mailbox by userid
	 */
	public function findTempMailboxByUser($id) {
		$criteria = new CDbCriteria;
		$criteria -> condition = 'user_id = :id and disabled is null';
		$criteria -> params = array(':id' => $id);

		return $this -> find($criteria);
	}
}
