<?php

use app\models\oldDb\OfferMix;
use app\models\pdf\OffersPdf;

$svg = '<svg viewBox="0 0 249 40" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M25.9451 39.1016C33.1797 36.8523 38.6537 30.6062 39.7846 22.9459C39.4686 23.522 39.1959 23.9247 38.9667 24.154C38.2244 24.8962 37.3338 25.2673 36.2947 25.2673C35.7009 25.2673 35.1442 25.0818 34.6247 24.7107C34.2536 24.4138 33.6598 24.2653 32.8433 24.2653C32.0269 24.2653 30.6167 24.4138 28.6127 24.7107C25.1984 25.3045 23.4913 25.6385 23.4913 25.7127C23.4913 25.9353 24.2707 26.9002 25.8293 28.6073C27.388 30.3887 28.1673 31.836 28.1673 32.9493C28.1673 33.6916 27.9447 34.508 27.4993 35.3987C27.3509 35.6956 27.0911 36.2893 26.72 37.18C26.4231 37.9965 26.2004 38.5531 26.052 38.85C26.0168 38.9361 25.9811 39.02 25.9451 39.1016ZM21.1238 39.969C20.7518 39.9896 20.3771 40 20 40C12.1679 40 5.38726 35.498 2.10476 28.9407C3.23834 28.9258 5.24586 28.6663 8.12733 28.162C10.5767 27.7167 12.1353 27.494 12.8033 27.494C13.3229 27.494 13.6569 27.6053 13.8053 27.828C14.1022 28.1991 14.9187 29.7207 16.2547 32.3927C17.5907 34.9905 18.8896 37.1429 20.1513 38.85C20.4832 39.2795 20.8073 39.6525 21.1238 39.969ZM0.687789 25.219C1.41231 24.6597 2.96438 23.7852 5.34399 22.5953L10.2427 20.146L7.90466 14.2453C6.56036 10.9966 5.7233 8.3608 5.39349 6.33791C2.04807 9.91311 0 14.7174 0 20C0 21.8055 0.239254 23.5552 0.687789 25.219ZM8.67689 3.51177C9.38441 4.28957 10.2032 5.49232 11.1333 7.12001C11.356 7.4169 11.7271 8.04779 12.2467 9.01268C12.8404 9.97757 13.36 10.8682 13.8053 11.6847C14.3249 12.5011 14.7702 13.2062 15.1413 13.8L17.4793 17.3627L21.2647 15.9153C29.2064 12.8722 34.5504 11.3507 37.2967 11.3507C37.5566 11.3507 37.8071 11.3576 38.0481 11.3714C34.8266 4.64525 27.9558 0 20 0C15.7967 0 11.8963 1.29665 8.67689 3.51177Z" fill="#8E0606"/>
<path d="M50.8667 31.6667V8.33333H60.7C61.8778 8.33333 63.0334 8.45556 64.1667 8.7C65.3223 8.94444 66.3445 9.36667 67.2334 9.96667C68.1445 10.5667 68.8778 11.3778 69.4334 12.4C69.9889 13.4 70.2667 14.6667 70.2667 16.2C70.2667 17.1778 70.1556 18.0444 69.9334 18.8C69.7111 19.5333 69.4 20.1889 69 20.7667C68.6 21.3222 68.1223 21.8 67.5667 22.2C67.0334 22.6 66.4445 22.9333 65.8 23.2L72.2334 31.6667H65.2667L59.7 24.0667H57.0334V31.6667H50.8667ZM57.0334 19.0667H60.9667C61.8556 19.0667 62.5667 18.8444 63.1 18.4C63.6556 17.9556 63.9334 17.2778 63.9334 16.3667C63.9334 15.4556 63.6556 14.7778 63.1 14.3333C62.5667 13.8889 61.8556 13.6667 60.9667 13.6667H57.0334V19.0667Z" fill="#00315D"/>
<path d="M72.3979 31.6667L81.5646 8.33333H88.2313L97.398 31.6667H91.0646L89.7313 28H80.0646L78.7313 31.6667H72.3979ZM81.898 23H87.898L85.498 16.2667C85.3868 15.9778 85.2868 15.6667 85.198 15.3333C85.1091 15 85.0091 14.5222 84.898 13.9C84.7868 14.5222 84.6868 15 84.5979 15.3333C84.5091 15.6667 84.4091 15.9778 84.298 16.2667L81.898 23Z" fill="#00315D"/>
<path d="M103.176 31.6667V23.7L94.0761 8.33333H101.076L106.243 17.9667L111.409 8.33333H118.409L109.309 23.7V31.6667H103.176Z" fill="#00315D"/>
<path d="M118.653 14.7C118.653 13.5444 118.886 12.5333 119.353 11.6667C119.82 10.8 120.453 10.0889 121.253 9.53333C122.053 8.95556 122.986 8.53333 124.053 8.26667C125.12 7.97778 126.253 7.83333 127.453 7.83333C128.92 7.83333 130.331 8 131.686 8.33333C133.064 8.66667 134.264 9.12222 135.286 9.7V15.3667C134.286 14.6111 133.153 14.0556 131.886 13.7C130.62 13.3222 129.309 13.1444 127.953 13.1667C126.82 13.1889 126.042 13.3333 125.62 13.6C125.198 13.8667 124.986 14.2222 124.986 14.6667C124.986 15.1778 125.286 15.6 125.886 15.9333C126.486 16.2444 127.231 16.5556 128.12 16.8667C129.031 17.1778 130.009 17.5222 131.053 17.9C132.098 18.2556 133.064 18.7333 133.953 19.3333C134.864 19.9333 135.62 20.6889 136.22 21.6C136.82 22.5111 137.12 23.6556 137.12 25.0333C137.12 26.4111 136.875 27.5667 136.386 28.5C135.898 29.4111 135.22 30.1444 134.353 30.7C133.509 31.2333 132.52 31.6111 131.386 31.8333C130.253 32.0556 129.053 32.1667 127.786 32.1667C126.098 32.1667 124.486 31.9889 122.953 31.6333C121.442 31.2556 120.064 30.7222 118.82 30.0333V24.2C120.22 25.1556 121.653 25.8333 123.12 26.2333C124.586 26.6333 125.986 26.8333 127.32 26.8333C128.542 26.8333 129.42 26.7222 129.953 26.5C130.509 26.2556 130.786 25.8444 130.786 25.2667C130.786 24.7111 130.486 24.2556 129.886 23.9C129.286 23.5444 128.531 23.2111 127.62 22.9C126.731 22.5667 125.764 22.2222 124.72 21.8667C123.675 21.4889 122.698 21 121.786 20.4C120.898 19.8 120.153 19.0556 119.553 18.1667C118.953 17.2556 118.653 16.1 118.653 14.7Z" fill="#00315D"/>
<path d="M144.013 31.6667L153.179 8.33333H159.846L169.013 31.6667H162.679L161.346 28H151.679L150.346 31.6667H144.013ZM153.513 23H159.513L157.113 16.2667C157.001 15.9778 156.901 15.6667 156.813 15.3333C156.724 15 156.624 14.5222 156.513 13.9C156.401 14.5222 156.301 15 156.213 15.3333C156.124 15.6667 156.024 15.9778 155.913 16.2667L153.513 23Z" fill="#00315D"/>
<path d="M171.244 31.6667V8.33333H181.078C182.255 8.33333 183.411 8.45556 184.544 8.7C185.7 8.94444 186.722 9.36667 187.611 9.96667C188.522 10.5667 189.255 11.3778 189.811 12.4C190.367 13.4 190.644 14.6667 190.644 16.2C190.644 17.1778 190.533 18.0444 190.311 18.8C190.089 19.5333 189.778 20.1889 189.378 20.7667C188.978 21.3222 188.5 21.8 187.944 22.2C187.411 22.6 186.822 22.9333 186.178 23.2L192.611 31.6667H185.644L180.078 24.0667H177.411V31.6667H171.244ZM177.411 19.0667H181.344C182.233 19.0667 182.944 18.8444 183.478 18.4C184.033 17.9556 184.311 17.2778 184.311 16.3667C184.311 15.4556 184.033 14.7778 183.478 14.3333C182.944 13.8889 182.233 13.6667 181.344 13.6667H177.411V19.0667Z" fill="#00315D"/>
<path d="M220.809 8.33333V31.6667H214.642V19.2667L209.642 26.9333H206.109L201.109 19.2333V31.6667H194.942V8.33333H200.776L207.876 19.2667L214.976 8.33333H220.809Z" fill="#00315D"/>
<path d="M223.049 31.6667L232.216 8.33333H238.882L248.049 31.6667H241.716L240.382 28H230.716L229.382 31.6667H223.049ZM232.549 23H238.549L236.149 16.2667C236.038 15.9778 235.938 15.6667 235.849 15.3333C235.76 15 235.66 14.5222 235.549 13.9C235.438 14.5222 235.338 15 235.249 15.3333C235.16 15.6667 235.06 15.9778 234.949 16.2667L232.549 23Z" fill="#00315D"/>
</svg>
';

$logo       = '<img src="data:image/svg+xml;base64,' . base64_encode($svg) . '" width="186" height="30" />';
$logoFooter = '<img src="data:image/svg+xml;base64,' . base64_encode($svg) . '" width="125" height="20" />';

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
							<p>САЙТ: <a href="https://readys.ru">READYS.RU</a></p>
						</li>
						<li>
							<i class="fas fa-circle"></i>
							<p>ПОЧТА: <a href="mailto:sklad@realtor.ru">SKLAD@REALTOR.RU</a></p>
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
				<a href="https://readys.ru">readys.ru</a>
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