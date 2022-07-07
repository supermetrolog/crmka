<?

use app\models\miniModels\TimelineStep;

$request = (object) $model->toArray();
?>
<p>
    Для запроса
    <a target="_blank" href='/companies/<?= $request->company_id ?>?request_id=<?= $request->id ?>&consultant_id=<?= $request->consultant_id ?>&step=<?= TimelineStep::OFFER_STEP_NUMBER ?>&<?= $newRecommendedOffersQueryString ?>'>
        <?= $request->name ?>
    </a>
    обновлена подборка <b class="text-success">+<?= $count ?></b>
<p>