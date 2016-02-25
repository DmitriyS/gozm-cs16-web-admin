<?php

class Maps extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bio_maps';
	}

	public function rules()
	{
		return array(
			array('games', 'numerical', 'integerOnly'=>true),
			array('map', 'length', 'max'=>100),
			array('id, map, games', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'map' => 'Карта',
			'games' => 'Игр сыграно',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('map',$this->map,true);
		$criteria->compare('games',$this->games);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

}