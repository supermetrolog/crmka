<?
$request = (object) $model->toArray();
$company = (object) $model->company->toArray();
?>
<p>
    За вами закреплен запрос <b><?= $request->name ?></b> в компании
    <a href='/companies/<?= $company->id ?>?selected_request=<?= $request->id ?>'><?= $company->full_name ?></a>
<p>