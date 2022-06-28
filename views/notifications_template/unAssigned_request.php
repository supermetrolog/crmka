<?
$request = (object) $model->toArray();
$company = (object) $model->company->toArray();
?>
<p>
    От вас откреплен запрос <b><?= $request->name ?></b> в компании
    <a href='/companies/<?= $company->id ?>?selected_request=<?= $request->id ?>'><?= $company->full_name ?></a>
<p>