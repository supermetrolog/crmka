<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Письмо</title>
	<style>
		.user-message {
			margin-bottom: 20px;
		}

		.signature {
			font-size: 11pt;
		}
	</style>
</head>
<body>
<div class="container">
	<?php if (!empty($userMessage)) : ?>
		<div class="user-message">
			<p><?= $userMessage ?></p>
		</div>
	<?php endif; ?>


	<?php if ($showSignature && !empty($user)) : ?>
		<div class="signature">
			<p>---</p>
			<p>
				С уважением, <strong><?php echo $user->userProfile->getMediumName() ?></strong><br>
				<span style="color:#696969"><?php echo $user->isOwner() ? 'Дирекция' : 'Менеджер' ?> по индустриальной недвижимости</span><br>
				<strong>Моб: <?php echo $user->userProfile->getFormattedPhone() ?></strong>, офис: +7 (495)
				150-03-23<br>
				Эл. почта: <a href="mailto:<?php echo $user->email ?>"><?php echo $user->email ?></a><br>
				Веб-сайт: <a href="https://www.raysarma.ru" target="_blank">www.raysarma.ru</a>
			</p>
			<p>
				<span style="font-size:9px;line-height:normal">Это сообщение и любые документы, приложенные к нему, содержат информацию, составляющую коммерческую тайну ООО «АРМА Проперти Эдвайзорз», 119019, Москва, ул. Знаменка, д.13, стр. 3. Если это сообщение не предназначено Вам, настоящим уведомляем Вас о том, что использование, копирование, распространение информации, содержащейся в настоящем сообщении, а также осуществление любых действий на основе этой информации, строго запрещено. Если Вы получили это сообщение по ошибке, пожалуйста, сообщите об этом отправителю по электронной почте и удалите это сообщение.</span>
			</p>
		</div>
	<?php endif; ?>
</div>
</body>
</html>