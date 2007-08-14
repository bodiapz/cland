<?php

/**
 * This is the model class for table "ticket_settings".
 *
 * The followings are the available columns in table 'ticket_settings':
 * @property integer $id
 * @property string $priority
 * @property string $emails
 * @property integer $frequency
 * @property integer $frequency_type
 * @property string $quantity
 * @property string $created_at
 * @property string $updated_at
 * @property string $disabled
 * The followings are the available model relations:
 * @property Users $user
 */
//CREATE TABLE smtp (id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, host varchar(255), username varchar(255), 
//password varchar(255), port int(11), security varchar(20), auth varchar(255))

class SmtpSettings extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'smtp';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('host, username, password, port, security, auth', 'safe'),
            /*array('user_id, subject, detail, created_at, updated_at', 'required'),
            array('user_id', 'numerical', 'integerOnly'=>true),
            array('tid, status', 'length', 'max'=>10),
            array('subject', 'length', 'max'=>255),
            array('priority', 'length', 'max'=>6),
            array('archived', 'safe'),*/
            // The following rule is used by search().

            array('id, host, username, password, port, security, auth', 'safe', 'on'=>'search'),
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
            //'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'host' => 'Host',
            'username' => 'username',
            'password' => 'Password',
            'port' => 'Port',
            'security' => 'Security',
            'auth' => 'Auth',
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

        $criteria=new CDbCriteria;

        /*$criteria->compare('id',$this->id);
        $criteria->compare('priority',$this->priority,true);
        $criteria->compare('emails',$this->emails);
        $criteria->compare('frequency',$this->frequency);
        $criteria->compare('frequency_type',$this->frequency_type);
        $criteria->compare('quantity',$this->quantity,true);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('updated_at',$this->updated_at,true);
        $criteria->compare('disabled',$this->disabled,true);*/

        $criteria -> order = 'id asc';

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Tickets the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
