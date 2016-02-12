<?php

class Players extends CActiveRecord
{
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
			array('nick', 'length', 'max'=>100),
			array('id, nick, ip, steam_id, last_seen, first_zombie, infect, zombiekills, humankills, death, infected, suicide, extra, knife_kills, best_zombie, best_human, best_player, escape_hero, rank, skill', 'safe', 'on'=>'search'),
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
			'nick'			    => 'Ник игрока',
			'ip'			    => 'IP игрока',
			'steam_id'		    => 'Steam игрока',
			'last_seen'			=> 'Замечен',
			'first_zombie'		=> 'Первый зм',
			'infect'		    => 'Заражения',
			'zombiekills'       => 'Убийста зм',
			'humankills'		=> 'Убийства людей',
			'death'		        => 'Смерти',
			'infected'		    => 'Заражён',
			'suicide'		    => 'Самоубийства',
			'extra'		        => 'Бонусы',
			'knife_kills'		=> 'Убийства с ножа',
			'best_zombie'		=> 'Лучший зм',
            'best_human'        => 'Лучший человек',
			'best_player'		=> 'Лучший игрок карты',
			'escape_hero'		=> 'Герой эскейпа',
            'rank'              => 'Ранк',
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
		$criteria->compare('last_seen',$this->last_seen);
		$criteria->compare('first_zombie',$this->first_zombie);
        $criteria->compare('infect',$this->infect);
		$criteria->compare('zombiekills',$this->zombiekills);
		$criteria->compare('humankills',$this->humankills);
        $criteria->compare('death',$this->death);
		$criteria->compare('infected',$this->infected);
		$criteria->compare('suicide',$this->suicide);
		$criteria->compare('extra',$this->extra);
        $criteria->compare('knife_kills',$this->knife_kills);
        $criteria->compare('best_zombie',$this->best_zombie);
        $criteria->compare('best_human',$this->best_human);
        $criteria->compare('best_player',$this->best_player);
        $criteria->compare('escape_hero',$this->escape_hero);
        $criteria->compare('rank',$this->rank);
        $criteria->compare('skill',$this->skill);

		$criteria->order = '`id` DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => Yii::app()->config->bans_per_page
			)
		));
	}

    public static function rankedPlayersSQL()
    {
        return "SELECT `nick`, `rank`, `skill` FROM
        	(SELECT *, (@_c := @_c + 1) AS `rank`,
         	((`infect` + `zombiekills`*2 + `humankills` + `knife_kills`*5 +
         	`best_zombie` + `best_human` + `best_player`*10 + `escape_hero`*3) / (`infected` + `death` + 300)) AS `skill`
			FROM (SELECT @_c := 0) r, `bio_players` ORDER BY `skill` DESC) AS `newtable`
			WHERE `rank` <= 10 ORDER BY `rank` ASC LIMIT 0, 10;";
    }

}
