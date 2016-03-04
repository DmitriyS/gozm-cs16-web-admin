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
			array('games, human_wins, zombie_wins', 'numerical', 'integerOnly'=>true),
			array('map', 'length', 'max'=>32),
			array('map', 'safe', 'on'=>'search'),
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
            'human_wins' => 'Побед людей',
            'zombie_wins' => 'Побед зомби',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('map',$this->map,true);
		$criteria->compare('games',$this->games);
        $criteria->compare('human_wins',$this->human_wins);
        $criteria->compare('zombie_wins',$this->zombie_wins);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

}
