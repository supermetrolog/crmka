<?php

use app\models\Company\Company;

/** @var Company $model */
?>
<p>
	За вами закреплена компания:
	<a href='/companies/<?= $model->id ?>'><?= $model->getFullName() ?></a>
<p>