<!DOCTYPE html>
<?php
/**
 * Тема default для сайта CS:Bans
 * Главный шаблон сайта
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/bootstrap-responsive.min.css" rel="stylesheet">

    <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,800italic,400,600,800&subset=latin,cyrillic" rel="stylesheet">

    <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/style.css" rel="stylesheet">
    <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/style-responsive.css" rel="stylesheet">

	<link rel="apple-touch-icon" sizes="57x57" href="/images/icons/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/images/icons/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/images/icons/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/images/icons/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/images/icons/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/images/icons/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/images/icons/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/images/icons/apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/images/icons/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="/images/icons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/images/icons/android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="/images/icons/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="/images/icons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/images/icons/manifest.json">
	<link rel="mask-icon" href="/images/icons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="/images/icons/favicon.ico">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-TileImage" content="/images/icons/mstile-144x144.png">
	<meta name="msapplication-config" content="/images/icons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
<body>

<div id="wrapper" class="clearfix">
	<div id="header">
		<div class="container">
			<h1>
				<?php
				echo CHtml::link(
						CHtml::encode(Yii::app()->name),
						Yii::app()->createUrl('/site/index')
					)
				?>
			</h1>
		</div>
	</div>
	<div id="nav" class="clearfix">
		<div class="container">
			<ul class="main-nav">
				<?php foreach(Usermenu::getMenu() as $item):?>
				<li>
					<?php echo CHtml::link(
							CHtml::encode($item['label']),
							$item['url']
						)?>
				</li>
				<?php endforeach;?>
			</ul>
			<ul class="main-nav pull-right">
				<li class="dropdown">
					<?php if(Yii::app()->user->isguest):?>
					<a href="javascript:;" data-toggle="dropdown">
						Войти
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li>
							<p>
								<form method="post" action="<?php echo Yii::app()->createUrl('/site/login')?>" accept-charset="UTF-8">
									<input style="margin-bottom: 15px;" type="text" placeholder="Логин" id="LoginForm_username" name="LoginForm[username]">
									<input style="margin-bottom: 15px;" type="password" placeholder="Пароль" id="LoginForm_password" name="LoginForm[password]">
									<input type="hidden" value="<?php echo Yii::app()->request->csrfToken?>" name="<?php echo Yii::app()->request->csrfTokenName?>" />
									<input class="btn btn-primary btn-block" name="yt0" type="submit" value="Войти">
								</form>
							</p>
						</li>
					</ul>
					<?php else: ?>
					<a href="javascript:;" data-toggle="dropdown">
						<?php echo Yii::app()->user->name ?>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<?php if(Webadmins::is_main_admin()):?>
						<li>
							<?php echo CHtml::link(
									'<i class="icon-globe"></i> Админцентр',
									Yii::app()->createUrl('/admin/index')
								)
							?>
						</li>
						<li>
							<hr />
						</li>
						<?php endif; ?>
						<li>
							<?php echo CHtml::link(
									'<i class="icon-off"></i> Выйти',
									Yii::app()->createUrl('/site/logout')
								)
							?>
						</li>
					</ul>
					<?php endif; ?>
				</li>
			</ul>
		</div>
	</div>

	<div id="wrap">
		<div class="container" id="page">
			<?php if(isset($this->breadcrumbs)):?>
				<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
					'links'=>$this->breadcrumbs,
					'htmlOptions' => array(
						'class' => 'page-title'
					)
				)); ?>
			<?php endif?>

			<?php echo $content; ?>

			<div class="clear"></div>
			<div id="push"></div>
		</div>
	</div>
	<br />
	<div id="copyright">
		<div class="container">
			<div class="row">
				<div id="lefts">
					<b><?php echo CHtml::encode(Yii::app()->name)?></b>
				</div>
				<div id="rights">
					<?php echo CHtml::link(
						'Группа ВКонтакте',
						'http://vk.com/go_zombie',
						array(
							'target' => '_blank'
						)
					)?>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="loading">
	<h1>Загрузка</h1>
	<div class="circle"></div>
	<div class="circle1"></div>
</div>
<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/theme.js"></script>
</body>
</html>
