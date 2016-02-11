<?php

class History extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{banhistory}}';
	}

	public function rules()
	{
		return array(
			array('player_nick', 'required'),
			array('ban_length', 'numerical', 'integerOnly'=>true),
			array('player_ip', 'match', 'pattern' => '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/'),
			array('player_id', 'match', 'pattern' => '/^(STEAM|VALVE)_([0-9]):([0-9]):\d{1,21}$/'),
			array('player_nick, ban_reason', 'length', 'max'=>100),
			array('ban_type', 'in', 'range' => array('S', 'SI')),
			//array('expiredTime', 'safe'),
			array('bhid, player_ip, player_id, player_nick, admin_ip, admin_id, admin_nick, ban_type, ban_reason, ban_created, ban_length, server_ip, server_name, unban_created, unban_reason, unban_admin_nick, map_name', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'commentsCount' => array(
                self::STAT,
                'Comments',
                'bhid',
                'defaultValue' => 0,
            ),
			'comments' => array(
                self::HAS_MANY,
                'Comments',
                'bhid',
                'order' => 'comments.id DESC',
            ),
			'filesCount' => array(
                self::STAT,
                'Files',
                'bhid',
                'defaultValue' => 0,
            ),
			'files' => array(
                self::HAS_MANY,
                'Files',
                'bhid',
                'order' => 'files.id DESC',
            ),
            'admin' => array(
                self::HAS_ONE,
                'Amxadmins',
                '',
                'on' => '`admin`.`steamid` = `t`.`admin_nick` OR '
                    . '`admin`.`steamid` = `t`.`admin_ip` OR '
                    . '`admin`.`steamid` = `t`.`admin_id`'
            )
		);
	}

	public function attributeLabels()
	{
		return array(
			'bhid'				=> 'Bid',
			'player_ip'			=> 'IP игрока',
			'player_id'			=> 'Steam  игрока',
			'player_nick'		=> 'Ник игрока',
			'admin_ip'			=> 'IP админа',
			'admin_id'			=> 'Steam ID админа',
			'admin_nick'		=> 'Админ',
			'ban_type'			=> 'Тип бана',
			'ban_reason'		=> 'Причина',
			'ban_created'		=> 'Дата',
			'ban_length'		=> 'Срок бана',
			'server_ip'			=> 'IP сервера',
			'server_name'		=> 'Название сервера',
            'unban_created'     => 'Время разбана',
            'unban_reason'      => 'Причина разбана',
            'unban_admin_nick'  => 'Разбан',
            'map_name'          => 'Карта',
			'city'				=> 'Город',
		);
	}

	/**
	 * Настройки поиска
	 * @return \CActiveDataProvider
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('bhid',$this->bid);
		$criteria->addSearchCondition('player_ip',$this->player_ip);
		$criteria->addSearchCondition('player_id',$this->player_id);
		$criteria->addSearchCondition('player_nick',$this->player_nick);
		$criteria->compare('admin_ip',$this->admin_ip,true);
		$criteria->compare('admin_id',$this->admin_id,true);
		if ($this->admin_nick) {
            $criteria->compare('admin_nick', $this->admin_nick, true);
        }
        $criteria->compare('ban_type',$this->ban_type,true);
		$criteria->addSearchCondition('ban_reason',$this->ban_reason,true);
		if ($this->ban_created) {
            $start = strtotime("{$this->ban_created} 00:00:00");
            $end = strtotime("{$this->ban_created} 23:59:59");
            $criteria->addBetweenCondition('ban_created', $start, $end);
        }
        $criteria->compare('ban_length',$this->ban_length);
		$criteria->compare('server_ip',$this->server_ip,true);
		$criteria->compare('server_name',$this->server_name,true);
        if ($this->unban_created) {
            $start = strtotime("{$this->unban_created} 00:00:00");
            $end = strtotime("{$this->unban_created} 23:59:59");
            $criteria->addBetweenCondition('unban_created', $start, $end);
        }
        $criteria->compare('unban_reason',$this->unban_reason,true);
        $criteria->compare('unban_admin_nick',$this->unban_admin_nick,true);
        $criteria->compare('map_name',$this->map_name,true);

		$criteria->order = '`bhid` DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => Yii::app()->config->bans_per_page
			)
		));
	}
}
