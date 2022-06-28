<?
$company = (object) $model->toArray();
?>
<p>
    От вас откреплена компания:
    <a href='/companies/<?= $company->id ?>'><?= $company->full_name ?></a>
<p>