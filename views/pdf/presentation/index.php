<?

use app\models\oldDb\OfferMix;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="http://<?= $model->getHost() ?>/css/null.css">
<link rel="stylesheet" href="http://<?= $model->getHost() ?>/css/pdf.css">
<title>Презентация</title>
<div id="header">
    <table>
        <tbody>
            <tr>
                <td class="logo">
                    <div class="image">
                        <img src="http://<?= $model->getHost() ?>/images/logo-plr.png" alt="">
                    </div>
                </td>
                <td class="consultant">
                    <div>
                        <p class="name"><?= $model->consultant ?></p>
                        <p class="position"> <span class="danger">Ведущий консультант</span> </p>
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
                                <p>САЙТ: <a href="https://industry.realtor.ru">INDUSTRY.REALTOR.RU</a></p>
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
                            <img src="http://<?= $model->getHost() ?>/images/logo-footer.png" alt="">
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
                                    <?
                                    if ($model->data->highway_name) : ?>
                                        <span class="btn-fake"><?= $model->data->highway_name ?></span>
                                    <? endif; ?>
                                    <? if ($model->data->highway_moscow_name) : ?>
                                        <span class="btn-fake"><?= $model->data->highway_moscow_name ?></span>
                                    <? endif; ?>
                                    <? if ($model->data->from_mkad) : ?>
                                        <span class="btn-fake"><?= $model->data->from_mkad ?> км от МКАД</span>
                                    <? endif; ?>
                                </div>

                            </div>

                        </div>
                    </div>
                </td>
                <td class="right">
                    <div class="image">
                        <img src="https://static-maps.yandex.ru/1.x/?ll=<?= $model->data->longitude ?>,<?= $model->data->latitude ?>&size=290,450&z=9&l=map&pt=<?= $model->data->longitude ?>,<?= $model->data->latitude ?>,pm2ntm" alt="">
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
                                        <p class="big"><?= $model->formatter->format($model->getAreaMax($model->data), 'decimal')  ?> м<sup>2</sup></p>
                                        <? if ($model->data->type_id == OfferMix::GENERAL_TYPE_ID && count($model->data->miniOffersMix) > 1) : ?>
                                            <p class="small">Деление от <span class="danger"><?= $model->getBlocksMinArea() ?> м<sup>2</sup></span></p>
                                        <? else : ?>
                                            <? if ($model->data->type_id == OfferMix::MINI_TYPE_ID && $model->getAreaMinSplit($model->data)) : ?>
                                                <p class="small">Деление от <span class="danger"><?= $model->getAreaMinSplit($model->data) ?> м<sup>2</sup></span></p>
                                            <? else : ?>
                                                <p class="small">Деление не предполагается</p>
                                            <? endif; ?>
                                        <? endif; ?>

                                    </div>
                                </td>
                                <td class="two">
                                    <div>
                                        <p><?= $model->getPriceLabel() ?><span class="danger"> <?= $model->getTaxInfo($model->data) ?> </span></p>
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
                                                                <img src="http://<?= $model->getHost() ?>/images/floors-icon.png" alt="">
                                                                <? if ($model->data->calc_floors) : ?>
                                                                    <p><?= $model->data->calc_floors ?> этаж</p>
                                                                <? else : ?>
                                                                    <p>—</p>
                                                                <? endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="item">
                                                        <div>
                                                            <div class="icon">
                                                                <img src="http://<?= $model->getHost() ?>/images/gates-icon.png" alt="">
                                                                <? if ($model->data->gate_num) : ?>
                                                                    <p><?= $model->data->gate_num ?> ворот</p>
                                                                <? else : ?>
                                                                    <p>—</p>
                                                                <? endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="item">
                                                        <div>
                                                            <div class="icon">
                                                                <img src="http://<?= $model->getHost() ?>/images/power-icon.png" alt="">
                                                                <? if ($model->data->power) : ?>
                                                                    <p><?= $model->numberFormat($model->data->power) ?> кВт</p>
                                                                <? else : ?>
                                                                    <p>—</p>
                                                                <? endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="item">
                                                        <div>
                                                            <div class="icon">
                                                                <img src="http://<?= $model->getHost() ?>/images/ceiling-icon.png" alt="">
                                                                <? if ($model->data->calc_ceilingHeight) : ?>
                                                                    <p><?= $model->data->calc_ceilingHeight ?> м</p>
                                                                <? else : ?>
                                                                    <p>—</p>
                                                                <? endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="item">
                                                        <div>
                                                            <div class="icon">
                                                                <img src="http://<?= $model->getHost() ?>/images/floor-icon.png" alt="">
                                                                <? if ($model->data->floor_type) : ?>
                                                                    <p><?= $model->data->floor_type ?></p>
                                                                <? else : ?>
                                                                    <p>—</p>
                                                                <? endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="item">
                                                        <div>
                                                            <div class="icon">
                                                                <!-- Канбалки -->
                                                                <!-- Внешняя отделка -->
                                                                <img src="http://<?= $model->getHost() ?>/images/crane-icon.png" alt="">
                                                                <? if ($model->data->cranes_cathead_capacity) : ?>
                                                                    <p><?= $model->data->cranes_cathead_capacity ?> тонн</p>
                                                                <? else : ?>
                                                                    <p>—</p>
                                                                <? endif; ?>
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
    <? if ($model->data->photos && count($model->data->photos) > 1 && $model->getBlocksCount() <= 6) : ?>
        <table class="photos">
            <tbody>
                <tr>
                    <? foreach ($model->getPhotosForBlock() as $photo) : ?>
                        <td class="<?= $photo['class'] ?>">
                            <div>
                                <img src="<?= $photo['src'] ?>" alt="">
                            </div>
                        </td>
                    <? endforeach; ?>
                </tr>
            </tbody>
        </table>
    <? endif; ?>
    <? if ($model->getBlocksCount() > 1) : ?>
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
                <? foreach ($model->data->miniOffersMix as $block) : ?>
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
                                <p><b><?= $model->formatter->format($model->getAreaMax($block), 'decimal')  ?></b> м<sup>2</sup></p>
                            </div>
                        </td>
                        <td class="four">
                            <div>
                                <p><?= $block->calc_ceilingHeight ?> м.</p>
                            </div>
                        </td>
                        <td class="five">
                            <div>
                                <p><?= $block->floor_type ? $block->floor_type  : 'нет' ?></p>
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
                                    <b><?= $model->getMaxPrice($block) ?></b> руб
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
                <? endforeach; ?>
            </tbody>
        </table>
    <? endif; ?>

    <? if ($model->data->photos && count($model->data->photos) > 4 && $model->getBlocksCount() <= 1) : ?>
        <table class="photos mt-header">
            <tbody>
                <tr>
                    <? foreach ($model->getPhotosForBlock(2) as $photo) : ?>
                        <td class="<?= $photo['class'] ?>">
                            <div>
                                <img src="<?= $photo['src'] ?>" alt="">
                            </div>
                        </td>
                    <? endforeach; ?>
                </tr>
            </tbody>
        </table>
    <? endif; ?>
    <? if ($model->getBlocksCount() <= 1 && count($model->data->photos) <= 1 && $model->data->auto_desc) : ?>
        <div class="title">
            <h3 class="two">Описание предложения</h3>
        </div>
        <div class="offer-description">
            <p>
                <?= $model->data->auto_desc ?>
            </p>
        </div>
    <? endif; ?>
    <hr>
    <? if ($model->data->photos && count($model->data->photos) > 4 && $model->getBlocksCount() > 1) : ?>
        <table class="photos mt-header p-0">
            <tbody>
                <tr>
                    <? foreach ($model->getPhotosForBlock(2) as $photo) : ?>
                        <td class="<?= $photo['class'] ?>">
                            <div>
                                <img src="<?= $photo['src'] ?>" alt="">
                            </div>
                        </td>
                    <? endforeach; ?>
                </tr>
            </tbody>
        </table>
        <div class="title">
            <h3 class="four">Характеристики</h3>
        </div>
    <? else : ?>
        <div class="title mt-header-min-min">
            <h3 class="four">Характеристики</h3>
        </div>
    <? endif; ?>
    <div class="parameters">
        <div class="list">
            <? foreach ($model->getParameterListOne() as $key => $params) : ?>
                <div class="item">
                    <h5><?= $key ?></h5>
                    <table>
                        <tbody>
                            <? foreach ($params as $label => $value) : ?>
                                <tr>
                                    <td>
                                        <p><?= $label ?></p>
                                    </td>
                                    <td>
                                        <? if ($model->isValidParameter($model->normalizeValue($value))) : ?>
                                            <p><?= $model->normalizeValue($value) . ' ' . $value['dimension'] ?></p>
                                        <? else : ?>
                                            <p>—</p>
                                        <? endif; ?>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <? endforeach; ?>
        </div>
        <div class="list">
            <? foreach ($model->getParameterListTwo() as $key => $params) : ?>
                <div class="item">
                    <h5><?= $key ?></h5>
                    <table>
                        <tbody>
                            <? foreach ($params as $label => $value) : ?>
                                <tr>
                                    <td>
                                        <p><?= $label ?></p>
                                    </td>
                                    <td>
                                        <? if ($model->isValidParameter($model->normalizeValue($value))) : ?>
                                            <p><?= $model->normalizeValue($value) . ' ' . $value['dimension'] ?></p>
                                        <? else : ?>
                                            <p>—</p>
                                        <? endif; ?>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <? endforeach; ?>
        </div>
    </div>

    <!-- <? if ($model->data->photos && count($model->data->photos) <= 4 || $model->getBlocksCount() <= 1) : ?>
        <table class="after-parameters mt-header">
            <div class="container">
                <img src="http://<?= $model->getHost() ?>/images/banner-bg.png" alt="">
                <div>
                    <h3>Узнайте первым о новом, подходящем Вам предложении</h3>
                    <p>Настройте параметры поиска подходящего Вам объекта и как только он появится на рынке, система автоматически пришлет его Вам на почту</p>
                    <a href="https://industry.realtor.ru">industry.realtor.ru</a>
                </div>
            </div>

        </table>
    <? endif; ?> -->
    <? if ($model->getBlocksCount() > 1 || count($model->data->photos) > 1 && $model->data->auto_desc) : ?>
        <hr>
    <? endif; ?>
    <? if ($model->getBlocksCount() > 6) : ?>
        <table class="photos mt-header">
            <tbody>
                <tr>
                    <? foreach ($model->getPhotosForBlock(1) as $photo) : ?>
                        <td class="<?= $photo['class'] ?>">
                            <div>
                                <img src="<?= $photo['src'] ?>" alt="">
                            </div>
                        </td>
                    <? endforeach; ?>
                </tr>
            </tbody>
        </table>
    <? endif; ?>
    <? if ($model->getBlocksCount() > 1 || count($model->data->photos) > 1 && $model->data->auto_desc) : ?>

        <div class="banner mt-header">
            <img src="http://<?= $model->getHost() ?>/images/banner-bg.png" alt="">
            <div>
                <h3>Узнайте первым о новом, подходящем Вам предложении</h3>
                <p>Настройте параметры поиска подходящего Вам объекта и как только он появится на рынке, система автоматически пришлет его Вам на почту</p>
                <a href="https://industry.realtor.ru">industry.realtor.ru</a>
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
    <? endif; ?>

</div>