<?php

class Players extends CActiveRecord
{
	public $rank;
	public $skill;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bio_players';
	}

	public function rules()
	{
		return array(
			array('nick', 'required'),
			array('ip', 'match', 'pattern' => '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/'),
			array('steam_id', 'match', 'pattern' => '/^(STEAM|VALVE)_([0-9]):([0-9]):\d{1,21}$/'),
			array('nick', 'length', 'max'=>32),
			array('id, nick, ip, steam_id, last_seen, damage, first_zombie, infect, zombiekills, humankills, death, infected, suicide, extra, knife_kills, awp_kills, grenade_kills, best_zombie, best_human, best_player, escape_hero, rank, skill', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array();
	}

	public function attributeLabels()
	{
		return array(
			'id'				=> 'Id',
			'nick'			    => 'Ник',
			'ip'			    => 'IP',
			'steam_id'		    => 'STEAM ID',
			'last_seen'			=> 'Замечен',
			'damage'			=> 'Урон',
			'first_zombie'		=> 'Первый зм',
			'infect'		    => 'Заражения',
			'zombiekills'       => 'Убийста зм',
			'humankills'		=> 'Убийства людей',
			'death'		        => 'Смерти',
			'infected'		    => 'Заражён',
			'suicide'		    => 'Самоубийства',
			'extra'		        => 'Бонусы',
			'knife_kills'		=> 'Убийства с ножа',
			'awp_kills'			=> 'Убийства с AWP',
			'grenade_kills'		=> 'Убийства гранатой',
			'best_zombie'		=> 'Лучший зм',
            'best_human'        => 'Лучший человек',
			'best_player'		=> 'Лучший игрок карты',
			'escape_hero'		=> 'Герой эскейпа',
            'rank'              => 'Место',
            'skill'             => 'Скилл',
		);
	}

	/**
	 * Настройки поиска
	 * @return \CActiveDataProvider
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('nick',$this->nick, true);
		$criteria->compare('ip',$this->ip, true);
		$criteria->compare('steam_id',$this->steam_id, true);
        if ($this->last_seen) {
            $start = strtotime("{$this->last_seen} 00:00:00");
            $end = strtotime("{$this->last_seen} 23:59:59");
            $criteria->addBetweenCondition('last_seen', $start, $end);
        }
        $criteria->compare('rank',$this->rank);
        $criteria->compare('skill',$this->skill);

		// $criteria->order = '`skill` DESC, `id` ASC';
		$criteria->order = Yii::app()->request->getParam('sort');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => Yii::app()->config->bans_per_page
			)
		));
	}

	public static function sql_skill_formula()
	{
		return 'ROUND((`infect` + `zombiekills`*2) / (`infected` + `death` + 300) * 1000)';
	}
}
