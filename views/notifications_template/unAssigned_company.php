<?php

use app\models\Company\Company;

/** @var Company $model */
?>
<p>
	От вас откреплена компания:
	<a href='/companies/<?= $model->id ?>'><?= $model->getFullName() ?></a>
<p>