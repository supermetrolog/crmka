<?php

use app\models\oldDb\OfferMix;
use app\models\pdf\OffersPdf;

$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 120" xml:space="preserve">
<g>
    <path fill="rgb(153, 8, 6)" d="M55.583,0.162C24.509,2.423,0,28.348,0,60c0,18.612,8.475,35.244,21.775,46.248l48.313-68.109
		L55.583,0.162z M91.021,8.632C81.968,3.153,71.353,0,60,0c-0.09,0-0.179,0.003-0.269,0.003l18.705,26.369L91.021,8.632z
		 M51.304,119.37c2.839,0.412,5.742,0.629,8.696,0.629c13.778,0,26.469-4.646,36.598-12.455l-20.389-53.38L51.304,119.37z
		 M93.097,9.951l-9.208,24.107l32.658,46.04C118.78,73.814,120,67.05,120,60C120,39.096,109.308,20.692,93.097,9.951z"/>
    <g>
		<path fill="#02305B" d="M198.091,90.629l20.031-52.443h15.279l20.025,52.443h-13.035l-3.823-9.998h-21.613l-3.819,9.998H198.091z
			 M217.85,73.085h15.837l-7.928-20.739L217.85,73.085z"/>
        <path fill="#02305B" d="M370.536,90.629l20.031-52.443h15.279l20.024,52.443h-13.034l-3.823-9.998H387.4l-3.819,9.998H370.536z
			 M390.295,73.085h15.837l-7.928-20.739L390.295,73.085z"/>
        <path fill="#02305B" d="M264.721,90.629V70.803l-23.137-32.617h15.156l15.559,21.934l15.559-21.934h15.155l-23.128,32.619v19.824
			H264.721z"/>
        <path fill="#02305B" d="M181.77,71.392c1.441-0.597,2.656-1.299,3.848-2.193c1.243-0.895,2.311-1.963,3.205-3.206
			c0.895-1.292,1.59-2.758,2.088-4.398c0.497-1.689,0.745-3.628,0.745-5.814c0-3.429-0.621-6.262-1.863-8.499
			c-1.243-2.286-2.883-4.1-4.92-5.442c-1.988-1.342-4.274-2.286-6.858-2.833c-2.535-0.547-5.119-0.82-7.753-0.82H138.32
			l4.443,11.633h28.34c1.914,0,3.446,0.478,4.594,1.435c1.196,0.957,1.794,2.417,1.794,4.379c0,1.961-0.598,3.421-1.794,4.378
			c-1.148,0.957-2.68,1.435-4.594,1.441H148.27v10.459l13.279,18.718V73.085h6.267l12.444,17.544h15.156L181.77,71.392z"/>
        <path fill="#02305B" d="M464.288,71.392c1.441-0.597,2.656-1.299,3.849-2.193c1.242-0.895,2.311-1.963,3.205-3.206
			c0.895-1.292,1.591-2.758,2.088-4.398c0.497-1.689,0.745-3.628,0.745-5.814c0-3.429-0.621-6.262-1.863-8.499
			c-1.243-2.286-2.884-4.1-4.92-5.442c-1.988-1.342-4.274-2.286-6.859-2.833c-2.534-0.547-5.119-0.82-7.753-0.82h-31.94
			l4.443,11.633h28.34c1.914,0,3.446,0.478,4.595,1.435c1.195,0.957,1.794,2.417,1.794,4.379c0,1.961-0.599,3.421-1.794,4.378
			c-1.148,0.957-2.681,1.435-4.595,1.441h-22.834v10.459l13.278,18.718V73.085h6.268l12.444,17.544h15.155L464.288,71.392z"/>
        <path fill="#02305B" d="M300.446,90.629h42.131c3.185,0,6.556-1.346,6.556-1.346c1.209-0.5,2.313-1.125,3.313-1.875
			c1.042-0.75,1.938-1.646,2.688-2.688c0.75-1.083,1.334-2.313,1.751-3.688c0.416-1.417,0.625-3.042,0.625-4.876
			c0-0.048-0.003-0.092-0.003-0.14s0.003-0.093,0.003-0.14c0-1.834-0.209-3.46-0.625-4.876c-0.417-1.376-1.001-2.605-1.751-3.689
			c-0.75-1.041-1.646-1.938-2.688-2.688c-1-0.751-5.013-3.171-12.878-3.171h-16.321c-1.916,0-3.449-0.494-4.6-1.453
			c-1.193-0.955-1.791-2.41-1.796-4.364c0.005-1.954,0.603-3.409,1.796-4.364c1.15-0.958,2.684-1.453,4.6-1.453h16.733l4.443-11.633
			h-23.925c-3.803,0-7.828,1.606-7.828,1.606c-1.443,0.598-2.762,1.344-3.956,2.239c-1.244,0.896-2.313,1.966-3.209,3.21
			c-0.896,1.294-1.593,2.762-2.091,4.404c-0.498,1.692-0.746,3.633-0.746,5.822c0,0.058,0.003,0.111,0.004,0.167
			c-0.001,0.057-0.004,0.11-0.004,0.167c0,2.19,0.248,4.13,0.746,5.823c0.498,1.643,1.194,3.11,2.091,4.404
			c0.896,1.244,1.965,2.314,3.209,3.21c1.194,0.896,5.985,3.846,15.377,3.846h14.499c1.24,0,2.232,0.337,2.977,0.957
			c0.772,0.618,1.16,1.56,1.162,2.824c-0.002,1.265-0.39,2.207-1.162,2.824c-0.744,0.62-1.736,0.931-2.977,0.931l-34.326,0.01
			L300.446,90.629z"/>
        <polygon fill="#02305B" points="527.109,38.186 511.456,60.254 495.801,38.186 482.702,38.186 482.702,90.629 496.476,90.629
			496.476,60.721 511.456,81.844 526.437,60.741 526.437,90.629 540.209,90.629 540.209,38.186 		"/>
        <path fill="#02305B" d="M544.665,90.629l20.031-52.443h15.279L600,90.629h-13.034l-3.823-9.998h-21.613l-3.819,9.998H544.665z
			 M564.424,73.085h15.837l-7.928-20.739L564.424,73.085z"/>
	</g>
</g>
</svg>
';

$logo       = '<img src="data:image/svg+xml;base64,' . base64_encode($svg) . '" width="175" height="35" />';
$logoFooter = '<img src="data:image/svg+xml;base64,' . base64_encode($svg) . '" width="120" height="24" />';

/** @var OffersPdf $model */
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet">
<link rel="stylesheet" href="<?= $model->getHost() ?>/css/null.css">
<link rel="stylesheet" href="<?= $model->getHost() ?>/css/pdf.css">
<title>Презентация</title>
<div id="header">
	<table>
		<tbody>
		<tr>
			<td class="logo">
				<div class="image">
					<?= $logo ?>
				</div>
			</td>
			<td class="consultant">
				<div>
					<p class="name"><?= $model->consultant ?></p>
					<p class="position"><span class="danger">Ведущий консультант</span></p>
				</div>
				<div class="phones">
					<p class="userPhone"><?= $model->getMainConsultantPhone() ?></p>
					<p class="companyPhone"><?= $model->getCompanyPhone() ?></p>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
</div>
<div id="footer">
	<table>
		<tbody>
		<tr>
			<td class="contacts">
				<div>
					<ul>
						<li>
							<p><b>ИНДУСТРИАЛЬНАЯ НЕДВИЖИМОСТЬ</b></p>
						</li>
						<li>
							<i class="fas fa-circle"></i>
							<p>ТЕЛ: <b>+7 495 150-03-23 </b></p>
						</li>
						<li>
							<i class="fas fa-circle"></i>
							<p>САЙТ: <a href="https://raysarma.ru">RAYSARMA.RU</a></p>
						</li>
						<li>
							<i class="fas fa-circle"></i>
							<p>ПОЧТА: <a href="mailto:info@raysarma.ru">INFO@RAYSARMA.RU</a></p>
						</li>
					</ul>
				</div>
			</td>
			<td class="image">
				<div>
					<div class="logo">
						<?= $logoFooter ?>
					</div>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
</div>

<div class="page no-absolute">
	<table class="main-info">
		<tbody>
		<tr>
			<td class="left">
				<div class="image">
					<img src="<?= $model->getPhoto() ?>" alt="">
					<div class="extra-info">
						<div class="object_id">
							<p> Объект <b><?= $model->data->object_id ?></b></p>
						</div>
						<div class="content">
							<p class="district"><?= $model->data->title ?></p>
							<p class="type"><?= $model->data->object_type_name ?></p>
							<div class="items">
								<span class="btn-fake"><?= $model->data->town_name ?></span>
								<?php
								if ($model->data->highway_name) : ?>
									<span class="btn-fake"><?= $model->data->highway_name ?></span>
								<?php endif; ?>
								<?php if ($model->data->highway_moscow_name) : ?>
									<span class="btn-fake"><?= $model->data->highway_moscow_name ?></span>
								<?php endif; ?>
								<?php if ($model->data->from_mkad) : ?>
									<span class="btn-fake"><?= $model->data->from_mkad ?> км от МКАД</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</td>
			<td class="right">
				<div class="image">
					<img src="https://static-maps.yandex.ru/1.x/?ll=<?= $model->data->longitude ?>,<?= $model->data->latitude ?>&size=290,450&z=9&l=map&pt=<?= $model->data->longitude ?>,<?= $model->data->latitude ?>,pm2ntm"
					     alt="">
				</div>
			</td>
		</tr>
		</tbody>
	</table>

	<table class="digital-info-container">
		<tbody>
		<tr>
			<td class="c_one">
				<table class="digital-info">
					<tbody>
					<tr>
						<td class="one">
							<div>
								<p><?= $model->getAreaLabel() ?></p>
								<p class="big"><?= $model->formatter->format($model->getArea($model->data), 'decimal') ?>
									м<sup>2</sup></p>
								<?php if ($model->data->type_id == OfferMix::GENERAL_TYPE_ID && $model->getAreaMinSplit($model->data)) : ?>
									<p class="small">Деление от <span
												class="danger"><?= $model->getAreaMinSplit($model->data) ?> м<sup>2</sup></span>
									</p>
								<?php else : ?>
									<?php if ($model->data->type_id == OfferMix::MINI_TYPE_ID && $model->getAreaMinSplit($model->data)) : ?>
										<p class="small">Деление от <span
													class="danger"><?= $model->getAreaMinSplit($model->data) ?> м<sup>2</sup></span>
										</p>
									<?php else : ?>
										<p class="small">Деление не предполагается</p>
									<?php endif; ?>
								<?php endif; ?>

							</div>
						</td>
						<td class="two">
							<div>
								<p><?= $model->getPriceLabel() ?><span
											class="danger"> <?= $model->getTaxInfo($model->data) ?> </span></p>
								<p class="big"><span class="danger"><?= $model->getPrice() ?> руб.</span></p>
								<p class="small"><?= $model->getExtraTax($model->data) ?></p>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
			<td class="c_two">
				<table class="digital-info">
					<tbody>
					<tr>
						<td class="three">
							<div>
								<table class="items">
									<tbody>
									<tr>
										<td class="item">
											<div>
												<div class="icon">
													<img src="<?= $model->getHost() ?>/images/floors-icon.png" alt="">
													<?php if ($model->data->calc_floors) : ?>
														<p><?= $model->data->calc_floors ?> этаж</p>
													<?php else : ?>
														<p>—</p>
													<?php endif; ?>
												</div>
											</div>
										</td>
										<td class="item">
											<div>
												<div class="icon">
													<img src="<?= $model->getHost() ?>/images/gates-icon.png" alt="">
													<?php if ($model->data->gate_num) : ?>
														<p><?= $model->data->gate_num ?> ворот</p>
													<?php else : ?>
														<p>—</p>
													<?php endif; ?>
												</div>
											</div>
										</td>
										<td class="item">
											<div>
												<div class="icon">
													<img src="<?= $model->getHost() ?>/images/power-icon.png" alt="">
													<?php if ($model->data->power) : ?>
														<p><?= $model->numberFormat($model->data->power) ?> кВт</p>
													<?php else : ?>
														<p>—</p>
													<?php endif; ?>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td class="item">
											<div>
												<div class="icon">
													<img src="<?= $model->getHost() ?>/images/ceiling-icon.png" alt="">
													<?php if ($model->data->calc_ceilingHeight) : ?>
														<p><?= $model->data->calc_ceilingHeight ?> м</p>
													<?php else : ?>
														<p>—</p>
													<?php endif; ?>
												</div>
											</div>
										</td>
										<td class="item">
											<div>
												<div class="icon">
													<img src="<?= $model->getHost() ?>/images/floor-icon.png" alt="">
													<?php if ($model->data->floor_type) : ?>
														<p><?= $model->data->floor_type ?></p>
													<?php else : ?>
														<p>—</p>
													<?php endif; ?>
												</div>
											</div>
										</td>
										<td class="item">
											<div>
												<div class="icon">
													<!-- Канбалки -->
													<!-- Внешняя отделка -->
													<img src="<?= $model->getHost() ?>/images/crane-icon.png" alt="">
													<?php if ($model->data->cranes_cathead_capacity) : ?>
														<p><?= $model->data->cranes_cathead_capacity ?> тонн</p>
													<?php else : ?>
														<p>—</p>
													<?php endif; ?>
												</div>
											</div>
										</td>
									</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		</tbody>
	</table>
	<?php if ($model->data->photos && count($model->data->photos) > 1 && $model->getBlocksCount() <= 6) : ?>
		<table class="photos">
			<tbody>
			<tr>
				<?php foreach ($model->getPhotosForBlock() as $photo) : ?>
					<td class="<?= $photo['class'] ?>">
						<div>
							<img src="<?= $photo['src'] ?>" alt="">
						</div>
					</td>
				<?php endforeach; ?>
			</tr>
			</tbody>
		</table>
	<?php endif; ?>
	<?php if ($model->getBlocksCount() > 1) : ?>
		<div class="title">
			<h3 class="one">Варианты деления</h3>
		</div>
		<table class="division-options">
			<tbody>
			<tr>
				<td class="one">
					<div>
						<p>ID блока</p>
					</div>
				</td>
				<td class="two">
					<div>
						<p>Этаж</p>
					</div>
				</td>
				<td class="three">
					<div>
						<p>Площадь</p>
					</div>
				</td>
				<td class="four">
					<div>
						<p>Высота</p>
					</div>
				</td>
				<td class="five">
					<div>
						<p>Тип пола</p>
					</div>
				</td>
				<td class="six">
					<div>
						<p>Ворота</p>
					</div>
				</td>
				<td class="seven">
					<div>
						<p>Отопление</p>
					</div>
				</td>
				<td class="eight">
					<div>
						<p>Ставка <b><?= $model->data->tax_form ?></b> м<sup>2</sup>/г</p>
					</div>
				</td>
				<td class="nine">
					<div>
						<p>Итого цена в месяц</p>
					</div>
				</td>
			</tr>
			<?php foreach ($model->data->miniOffersMix as $block) : ?>
				<tr>
					<td class="one">
						<div>
							<p><?= $block->visual_id ?></p>
						</div>
					</td>
					<td class="two">
						<div>
							<p><?= $block->calc_floors ?></p>
						</div>
					</td>
					<td class="three">
						<div>
							<p><b><?= $model->formatter->format($model->getAreaMax($block), 'decimal') ?></b>
								м<sup>2</sup></p>
						</div>
					</td>
					<td class="four">
						<div>
							<p><?= $block->calc_ceilingHeight ?> м.</p>
						</div>
					</td>
					<td class="five">
						<div>
							<p><?= $block->floor_type ? $block->floor_type : 'нет' ?></p>
						</div>
					</td>
					<td class="six">
						<div>
							<p><?= $block->gate_type ?></p>
						</div>
					</td>
					<td class="seven">
						<div>
							<p><?= $model->getHeated($block) ?></p>
						</div>
					</td>
					<td class="eight">
						<div>
							<p>
								<b><?= $model->getMinPrice($block) ?></b> руб
								<?= $model->getOpex($block) == 3 ? ' + OPEX' : '' ?>
								<?= $block->public_services == 3 ? ' + КУ' : '' ?>
							</p>
						</div>
					</td>
					<td class="nine">
						<div>
							<p><?= $model->getTotalPrice($block) ?> руб</p>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<?php if ($model->data->photos && count($model->data->photos) > 4 && $model->getBlocksCount() <= 1) : ?>
		<table class="photos mt-header">
			<tbody>
			<tr>
				<?php foreach ($model->getPhotosForBlock(2) as $photo) : ?>
					<td class="<?= $photo['class'] ?>">
						<div>
							<img src="<?= $photo['src'] ?>" alt="">
						</div>
					</td>
				<?php endforeach; ?>
			</tr>
			</tbody>
		</table>
	<?php endif; ?>
	<?php if ($model->getBlocksCount() <= 1 && count($model->data->photos) <= 1 && $model->data->auto_desc) : ?>
		<div class="title">
			<h3 class="two">Описание предложения</h3>
		</div>
		<div class="offer-description">
			<p>
				<?= $model->data->auto_desc ?>
			</p>
		</div>
	<?php endif; ?>
	<hr>
	<?php if ($model->data->photos && count($model->data->photos) > 4 && $model->getBlocksCount() > 1) : ?>
		<table class="photos mt-header p-0">
			<tbody>
			<tr>
				<?php foreach ($model->getPhotosForBlock(2) as $photo) : ?>
					<td class="<?= $photo['class'] ?>">
						<div>
							<img src="<?= $photo['src'] ?>" alt="">
						</div>
					</td>
				<?php endforeach; ?>
			</tr>
			</tbody>
		</table>
		<div class="title">
			<h3 class="four">Характеристики</h3>
		</div>
	<?php else : ?>
		<div class="title mt-header-min">
			<h3 class="four">Характеристики</h3>
		</div>
	<?php endif; ?>
	<div class="parameters">
		<div class="list">
			<?php foreach ($model->getParameterListOne() as $key => $params) : ?>
				<div class="item">
					<h5><?= $key ?></h5>
					<table>
						<tbody>
						<?php foreach ($params as $label => $value) : ?>
							<tr>
								<td>
									<p><?= $label ?></p>
								</td>
								<td>
									<?php if ($model->isValidParameter($model->normalizeValue($value))) : ?>
										<p><?= $model->normalizeValue($value) . ' ' . $value['dimension'] ?></p>
									<?php else : ?>
										<p>—</p>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="list">
			<?php foreach ($model->getParameterListTwo() as $key => $params) : ?>
				<div class="item">
					<h5><?= $key ?></h5>
					<table>
						<tbody>
						<?php foreach ($params as $label => $value) : ?>
							<tr>
								<td>
									<p><?= $label ?></p>
								</td>
								<td>
									<?php if ($model->isValidParameter($model->normalizeValue($value))) : ?>
										<p><?= $model->normalizeValue($value) . ' ' . $value['dimension'] ?></p>
									<?php else : ?>
										<p>—</p>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php if ($model->getBlocksCount() > 1 || count($model->data->photos) > 1 && $model->data->auto_desc) : ?>
		<hr>
	<?php endif; ?>
	<?php if ($model->getBlocksCount() > 6) : ?>
		<table class="photos mt-header">
			<tbody>
			<tr>
				<?php foreach ($model->getPhotosForBlock(1) as $photo) : ?>
					<td class="<?= $photo['class'] ?>">
						<div>
							<img src="<?= $photo['src'] ?>" alt="">
						</div>
					</td>
				<?php endforeach; ?>
			</tr>
			</tbody>
		</table>
	<?php endif; ?>
	<?php if ($model->getBlocksCount() > 1 || count($model->data->photos) > 1 && $model->data->auto_desc) : ?>

		<div class="banner mt-header">
			<img src="<?= $model->getHost() ?>/images/banner-bg.png" alt="">
			<div>
				<h3>Узнайте первым о новом, подходящем Вам предложении</h3>
				<p>Настройте параметры поиска подходящего Вам объекта и как только он появится на рынке, система
					автоматически пришлет его Вам на почту</p>
				<a href="https://raysarma.ru">raysarma.ru</a>
			</div>
		</div>
		<div class="title">
			<h3 class="two">Описание предложения</h3>
		</div>
		<div class="offer-description">
			<p>
				<?= $model->data->auto_desc ?>
			</p>
		</div>
	<?php endif; ?>

</div>