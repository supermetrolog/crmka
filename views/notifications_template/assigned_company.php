<?
$company = (object) $model->toArray();
?>
<p>
    За вами закреплена компания:
    <a href='/companies/<?= $company->id ?>'><?= $company->full_name ?></a>
<p>