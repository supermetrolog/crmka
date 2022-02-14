    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
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
                            <!-- <p class="name"><? //= $data->agent_name 
                                                    ?></p> -->
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
    <div class="page-no-absolute">
        <table class="main-info">
            <tbody>
                <tr>
                    <td class="left">
                        <div class="image">
                            <img src="<?= $data->photos[0] ?>" alt="">
                            <div class="extra-info">
                                <div class="object_id">
                                    Объект <?= $data->object_id ?>
                                </div>
                                <div class="content">
                                    <p class="district"><?= $data->district_name ?></p>
                                    <p class="type"><?= $data->object_type_name ?></p>
                                    <div class="items">
                                        <span class="btn-fake"><?= $data->town_name ?></span>
                                        <span class="btn-fake"><?= $data->highway_name ?></span>
                                        <span class="btn-fake"><?= $data->from_mkad ?> от МКАД</span>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </td>
                    <td class="right">
                        <div class="image">
                            <img src="https://static-maps.yandex.ru/1.x/?ll=<?= $data->longitude ?>,<?= $data->latitude ?>&size=290,450&z=9&l=map&pt=<?= $data->longitude ?>,<?= $data->latitude ?>,pm2ntm" alt="">
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
                                            <p>ПЛОЩАДИ В АРЕНДУ</p>
                                            <p class="big"><?= $model->normalizeNumber($data->area_max) ?> м<sup>2</sup></p>
                                            <? if ($data->area_max > $data->area_min) : ?>
                                                <p class="small">Деление <span class="danger">от <?= $model->normalizeNumber($data->area_min) ?> м<sup>2</sup></span></p>
                                            <? else : ?>
                                                <p class="small">Деление не предполагается</p>
                                            <? endif; ?>
                                        </div>
                                    </td>
                                    <td class="two">
                                        <div>
                                            <p>СРЕДНЯЯ СТАВКА ЗА М<sup>2</sup>/ГОД, <span class="danger"><?= $data->tax_form ?> </span></p>
                                            <!-- <p class="big"><span class="danger"><? //= $model->normalizeNumber($data->price_sale_max) 
                                                                                        ?> руб.</span></p> -->
                                            <p class="big"><span class="danger"><?= $model->normalizeNumber($data->price_floor_max) ?> руб.</span></p>
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
                                                                    <p><?= $data->general_stats->floors ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="item">
                                                            <div>
                                                                <div class="icon">
                                                                    <img src="http://<?= $model->getHost() ?>/images/gates-icon.png" alt="">
                                                                    <p><?= $data->general_stats->gates ?></p>

                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="item">
                                                            <div>
                                                                <div class="icon">
                                                                    <img src="http://<?= $model->getHost() ?>/images/power-icon.png" alt="">
                                                                    <p><?= $model->getPower($data->general_stats) ?></p>

                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="item">
                                                            <div>
                                                                <div class="icon">
                                                                    <img src="http://<?= $model->getHost() ?>/images/ceiling-icon.png" alt="">
                                                                    <p><?= $data->general_stats->ceiling ?></p>

                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="item">
                                                            <div>
                                                                <div class="icon">
                                                                    <img src="http://<?= $model->getHost() ?>/images/floor-icon.png" alt="">
                                                                    <p><?= $data->general_stats->floor ?></p>

                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="item">
                                                            <div>
                                                                <div class="icon">
                                                                    <img src="http://<?= $model->getHost() ?>/images/crane-icon.png" alt="">
                                                                    <p><?= $data->general_stats->cranes ?></p>

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
        <table class="photos">
            <tbody>
                <tr>
                    <td class="one">
                        <div>
                            <img src="<?= $data->photos[1] ?>" alt="">
                        </div>
                    </td>
                    <td class="two">
                        <div>
                            <img src="<?= $data->photos[2] ?>" alt="">
                        </div>
                    </td>
                    <td class="three">
                        <div>
                            <img src="<?= $data->photos[3] ?>" alt="">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <? if ($model->devisionCount) : ?>
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
                                <p>Температура храниения</p>
                            </div>
                        </td>
                        <td class="eight">
                            <div>
                                <p>Ставка <b>без НДС</b> м<sup>2</sup>/г</p>
                            </div>
                        </td>
                        <td class="nine">
                            <div>
                                <p>Итого цена в месяц</p>
                            </div>
                        </td>
                    </tr>
                    <? foreach ($data->blocks as $block) : ?>
                        <tr>
                            <td class="one">
                                <div>
                                    <p><?= $block->object_id ?></p>
                                </div>
                            </td>
                            <td class="two">
                                <div>
                                    <p><?= $block->floor_min ?></p>
                                </div>
                            </td>
                            <td class="three">
                                <div>
                                    <p><b><?= $model->getBlockArea($block) ?></b> м<sup>2</sup></p>
                                </div>
                            </td>
                            <td class="four">
                                <div>
                                    <p><?= $block->ceiling_height_min . '-' . $block->ceiling_height_max ?> м.</p>
                                </div>
                            </td>
                            <td class="five">
                                <div>
                                    <p><?= $block->floor_type ?></p>
                                </div>
                            </td>
                            <td class="six">
                                <div>
                                    <p><?= $block->gate_type ?></p>
                                </div>
                            </td>
                            <td class="seven">
                                <div>
                                    <p><?= $model->getHeating($block) ?></p>
                                </div>
                            </td>
                            <td class="eight">
                                <div>
                                    <p><b><?= $model->getPrice($block) ?></b> руб</p>
                                </div>
                            </td>
                            <td class="nine">
                                <div>
                                    <p><?= $model->getTotal($block) ?> руб</p>
                                </div>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>
        <? endif; ?>
        <? if ($model->devisionCount < 7) : ?>
            <hr>
        <? endif; ?>
        <table class="photos">
            <tbody>
                <tr>
                    <td class="one">
                        <div>
                            <img src="<?= $data->photos[4] ?>" alt="">
                        </div>
                    </td>
                    <td class="two">
                        <div>
                            <img src="<?= $data->photos[5] ?>" alt="">
                        </div>
                    </td>
                    <td class="three">
                        <div>
                            <img src="<?= $data->photos[6] ?>" alt="">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="title">
            <h3 class="three">Подробные параметры</h3>
        </div>
        <div class="div">
            <table class="params-new">
                <tbody>
                    <tr>
                        <td class="one">
                            <div class="params-title">
                                <p>Площади к аренде</p>
                            </div>
                        </td>
                        <td class="two">
                        </td>
                        <td class="three">
                            <div class="params-title">
                                <p>Коммуникации</p>
                            </div>
                        </td>
                        <td class="four">
                        </td>
                    </tr>
                    <tr class="even">
                        <td class="one">
                            <div>
                                <p>Свободная площадь</p>
                            </div>
                        </td>
                        <td class="two">
                            <div>
                                <p><?= $model->normalizeText($data->stats->areas[1]->area[1]) ?></p>
                            </div>
                        </td>
                        <td class="three">
                            <div>
                                <p>Электричество</p>
                            </div>
                        </td>
                        <td class="four">
                            <div>
                                <p><?= $model->normalizeText($data->stats->communications[1]->power[1]) ?></p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="params-new">
                <tbody>
                    <tr>
                        <td class="one">
                            <div>
                                <p>Из них мезонина</p>
                            </div>
                        </td>
                        <td class="two">
                            <div>
                                <p><?= $model->normalizeText($data->stats->areas[1]->area_mezzanine[1]) ?></p>
                            </div>
                        </td>
                        <td class="three">
                            <div>
                                <p>Отопление</p>
                            </div>
                        </td>
                        <td class="four">
                            <div>
                                <p><?= $model->normalizeText($data->stats->communications[1]->heating[1]) ?></p>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="params-new even">
                <tbody>
                    <tr>
                        <td class="one">
                            <div>
                                <p>Из них офисов</p>
                            </div>
                        </td>
                        <td class="two">
                            <div>
                                <p><?= $model->normalizeText($data->stats->areas[1]->area_office[1]) ?></p>
                            </div>
                        </td>
                        <td class="three">
                            <div>
                                <p>Водоснабжение</p>
                            </div>
                        </td>
                        <td class="four">
                            <div>
                                <p><?= $model->normalizeText($data->stats->communications[1]->water[1]) ?></p>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="params-new">
                <tbody>
                    <tr>
                        <td class="one">
                            <div>
                                <p>Вместимость</p>
                            </div>
                        </td>
                        <td class="two">
                            <div>
                                <p><?= $model->normalizeText($data->stats->areas[1]->pallet_place[1]) ?></p>

                            </div>
                        </td>
                        <td class="three">
                            <div>
                                <p>Канализация</p>
                            </div>
                        </td>
                        <td class="four">
                            <div>
                                <p><?= $model->normalizeText($data->stats->communications[1]->sewage_central[1]) ?></p>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="params-new even">
                <tbody>
                    <tr>
                        <td class="one">
                            <div>
                                <p>Уличное хранение</p>
                            </div>
                        </td>
                        <td class="two">
                            <div>
                                <p><?= $model->normalizeText($data->stats->areas[1]->area_field[1]) ?></p>
                            </div>
                        </td>
                        <td class="three">
                            <div>
                                <p>Внтиляция</p>
                            </div>
                        </td>
                        <td class="four">
                            <div>
                                <p><?= $model->normalizeText($data->stats->communications[1]->ventilation[1]) ?></p>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                </td>
                <td class="two">
                </td>
                <td class="three">
                    <div>
                        <p>Газ</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->communications[1]->gas[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                <div class="params-title">
                    <p>Характеристики</p>
                </div>
                </td>
                <td class="two">
                </td>
                <td class="three">
                    <div>
                        <p>Пар</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->communications[1]->steam[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new even">
                <tbody>
                    <tr>
                        <td class="one">
                            <div>
                                <p>Этажность</p>
                            </div>
                        </td>
                        <td class="two">
                            <div>
                                <p><?= $model->normalizeText($data->stats->options[1]->floor[1]) ?></p>
                            </div>
                        </td>
                        <td class="three">
                            <div>
                                <p>Телефония</p>
                            </div>
                        </td>
                        <td class="four">
                            <div>
                                <p><?= $model->normalizeText($data->stats->communications[1]->phone[1]) ?></p>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Класс объекта</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->options[1]->class_name[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Интернет</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->communications[1]->internet[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Высота потолков</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->options[1]->ceiling_height[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                </td>
                <td class="four">
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Тип ворот</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->options[1]->gate_type[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                    <div class="params-title">
                        <p>Ж/Д и крановые устр-ва</p>
                    </div>
                </td>
                <td class="four">
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Количество ворот</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->options[1]->gate[1]) ?></p>
                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Ж/Д ветка</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->cranes[1]->railway[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Стеллажи</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->options[1]->racks[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Козловые краны</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->cranes[1]->cranes_gantry[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Нагрузка на пол</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->options[1]->load_floor[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Ж/Д краны</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->cranes[1]->cranes_railway[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Нагрузка на мезонин</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->options[1]->load_mezzanine[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Мостовые краны</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->cranes[1]->cranes_overhead[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Температура</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->options[1]->temperature[1]) ?></p>
                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Кран-балки</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->cranes[1]->cranes_cathead[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Шаг колон</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->options[1]->column_grid[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Тельферы</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->cranes[1]->telphers[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Грузовые лифты</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->options[1]->elevators[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                </td>
                <td class="four">
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                </td>
                <td class="two">
                </td>
                <td class="three">
                    <div class="params-title">
                        <p>Инфраструктура</p>
                    </div>
                </td>
                <td class="four">
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
                <div class="params-title">
                    <p class="params-title">Безопасность</p>
                </div>
                </td>
                <td class="two">
                </td>
                <td class="three">
                    <div>
                        <p>Въезд на территорию</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->infrastructure[1]->entry_territory[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Охрана объекта</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->security[1]->guard[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Парковка легковая</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->infrastructure[1]->parking_car[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Пожаротушение</p>

                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->security[1]->firefighting[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Парковка грузовая</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->infrastructure[1]->parking_truck[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Видеонаблюдение</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->security[1]->video_control[1]) ?></p>
                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Столовая/кафе</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->infrastructure[1]->canteen[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Контроль доступа</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->security[1]->access_control[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                    <div>
                        <p>Общежитие</p>
                    </div>
                </td>
                <td class="four">
                    <div>
                        <p><?= $model->normalizeText($data->stats->infrastructure[1]->hostel[1]) ?></p>

                    </div>
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Охранная сигнализация</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->security[1]->security_alert[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                </td>
                <td class="four">
                </td>
                </tr>
                </tbody>
            </table>
            <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
                <div>
                    <p>Пожарная сигнализация</p>
                </div>
                </td>
                <td class="two">
                    <div>
                        <p><?= $model->normalizeText($data->stats->security[1]->fire_alert[1]) ?></p>

                    </div>
                </td>
                <td class="three">
                </td>
                <td class="four">
                </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="banner">
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
                <?= $data->description ?>
            </p>
        </div>
    </div>
    <!-- <div class="page-no-absolute">

        <table class="photos">
            <tbody>
                <tr>
                    <td class="one">
                        <div>
                            <img src="<?= $data->photos[4] ?>" alt="">
                        </div>
                    </td>
                    <td class="two">
                        <div>
                            <img src="<?= $data->photos[5] ?>" alt="">
                        </div>
                    </td>
                    <td class="three">
                        <div>
                            <img src="<?= $data->photos[6] ?>" alt="">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="title">
            <h3 class="two">Описание предложения</h3>
        </div>
        <div class="offer-description">
            <p>
                Повседневная практика показывает, что сложившаяся структура организации влечет за собой процесс внедрения и модернизации направлений прогрессивного развития. Равным образом постоянный количественный рост и сфера нашей активности позволяет выполнять важные задания по разработке соответствующий условий активизации. Разнообразный и богатый опыт новая модель организационной деятельности позволяет оценить значение существенных финансовых и административных условий. Таким образом сложившаяся структура организации требуют определения и уточнения существенных финансовых и административных условий.

                Равным образом дальнейшее развитие различных форм деятельности в значительной степени обуславливает создание систем массового участия. Задача организации, в особенности же сложившаяся структура организации позволяет оценить значение модели развития. С другой стороны рамки и место обучения кадров требуют определения и уточнения систем массового участия. Равным образом дальнейшее развитие различных форм деятельности способствует подготовки и реализации новых предложений. Товарищи! реализация намеченных плановых заданий в значительной степени обуславливает создание дальнейших направлений развития.
            </p>
        </div>

        <div class="banner">
            <img src="http://<?= $model->getHost() ?>/images/banner-bg.png" alt="">
            <div>
                <h3>Узнайте первым о новом, подходящем Вам предложении</h3>
                <p>Настройте параметры поиска подходящего Вам объекта и как только он появится на рынке, система автоматически пришлет его Вам на почту</p>
                <a href="https://industry.realtor.ru">industry.realtor.ru</a>
            </div>
        </div>
        <div class="title">
            <h3 class="three">Подробные параметры</h3>
        </div>
        <table class="params-new">
            <tbody>
                <tr>
                    <td class="one">
                        <div class="params-title">
                            <p>Площади к аренде</p>
                        </div>
                    </td>
                    <td class="two">
                    </td>
                    <td class="three">
                        <div class="params-title">
                            <p>Коммуникации</p>
                        </div>
                    </td>
                    <td class="four">
                    </td>
                </tr>
                <tr class="even">
                    <td class="one">
                        <div>
                            <p>Свободная площадь</p>
                        </div>
                    </td>
                    <td class="two">
                        <div>
                            <p><?= $model->normalizeText($data->stats->areas[1]->area[1]) ?></p>
                        </div>
                    </td>
                    <td class="three">
                        <div>
                            <p>Электричество</p>
                        </div>
                    </td>
                    <td class="four">
                        <div>
                            <p><?= $model->normalizeText($data->stats->communications[1]->power[1]) ?></p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="params-new">
            <tbody>
                <tr>
                    <td class="one">
                        <div>
                            <p>Из них мезонина</p>
                        </div>
                    </td>
                    <td class="two">
                        <div>
                            <p><?= $model->normalizeText($data->stats->areas[1]->area_mezzanine[1]) ?></p>
                        </div>
                    </td>
                    <td class="three">
                        <div>
                            <p>Отопление</p>
                        </div>
                    </td>
                    <td class="four">
                        <div>
                            <p><?= $model->normalizeText($data->stats->communications[1]->heating[1]) ?></p>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="params-new even">
            <tbody>
                <tr>
                    <td class="one">
                        <div>
                            <p>Из них офисов</p>
                        </div>
                    </td>
                    <td class="two">
                        <div>
                            <p><?= $model->normalizeText($data->stats->areas[1]->area_office[1]) ?></p>
                        </div>
                    </td>
                    <td class="three">
                        <div>
                            <p>Водоснабжение</p>
                        </div>
                    </td>
                    <td class="four">
                        <div>
                            <p><?= $model->normalizeText($data->stats->communications[1]->water[1]) ?></p>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="params-new">
            <tbody>
                <tr>
                    <td class="one">
                        <div>
                            <p>Вместимость</p>
                        </div>
                    </td>
                    <td class="two">
                        <div>
                            <p><?= $model->normalizeText($data->stats->areas[1]->pallet_place[1]) ?></p>

                        </div>
                    </td>
                    <td class="three">
                        <div>
                            <p>Канализация</p>
                        </div>
                    </td>
                    <td class="four">
                        <div>
                            <p><?= $model->normalizeText($data->stats->communications[1]->sewage_central[1]) ?></p>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="params-new even">
            <tbody>
                <tr>
                    <td class="one">
                        <div>
                            <p>Уличное хранение</p>
                        </div>
                    </td>
                    <td class="two">
                        <div>
                            <p><?= $model->normalizeText($data->stats->areas[1]->area_field[1]) ?></p>
                        </div>
                    </td>
                    <td class="three">
                        <div>
                            <p>Внтиляция</p>
                        </div>
                    </td>
                    <td class="four">
                        <div>
                            <p><?= $model->normalizeText($data->stats->communications[1]->ventilation[1]) ?></p>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            </td>
            <td class="two">
            </td>
            <td class="three">
                <div>
                    <p>Газ</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->communications[1]->gas[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            <div class="params-title">
                <p>Характеристики</p>
            </div>
            </td>
            <td class="two">
            </td>
            <td class="three">
                <div>
                    <p>Пар</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->communications[1]->steam[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new even">
            <tbody>
                <tr>
                    <td class="one">
                        <div>
                            <p>Этажность</p>
                        </div>
                    </td>
                    <td class="two">
                        <div>
                            <p><?= $model->normalizeText($data->stats->options[1]->floor[1]) ?></p>
                        </div>
                    </td>
                    <td class="three">
                        <div>
                            <p>Телефония</p>
                        </div>
                    </td>
                    <td class="four">
                        <div>
                            <p><?= $model->normalizeText($data->stats->communications[1]->phone[1]) ?></p>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Класс объекта</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->options[1]->class_name[1]) ?></p>

                </div>
            </td>
            <td class="three">
                <div>
                    <p>Интернет</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->communications[1]->internet[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Высота потолков</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->options[1]->ceiling_height[1]) ?></p>

                </div>
            </td>
            <td class="three">
            </td>
            <td class="four">
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Тип ворот</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->options[1]->gate_type[1]) ?></p>

                </div>
            </td>
            <td class="three">
                <div class="params-title">
                    <p>Ж/Д и крановые устр-ва</p>
                </div>
            </td>
            <td class="four">
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Количество ворот</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->options[1]->gate[1]) ?></p>
                </div>
            </td>
            <td class="three">
                <div>
                    <p>Ж/Д ветка</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->cranes[1]->railway[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Стеллажи</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->options[1]->racks[1]) ?></p>

                </div>
            </td>
            <td class="three">
                <div>
                    <p>Козловые краны</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->cranes[1]->cranes_gantry[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Нагрузка на пол</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->options[1]->load_floor[1]) ?></p>

                </div>
            </td>
            <td class="three">
                <div>
                    <p>Ж/Д краны</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->cranes[1]->cranes_railway[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Нагрузка на мезонин</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->options[1]->load_mezzanine[1]) ?></p>

                </div>
            </td>
            <td class="three">
                <div>
                    <p>Мостовые краны</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->cranes[1]->cranes_overhead[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Температура</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->options[1]->temperature[1]) ?></p>
                </div>
            </td>
            <td class="three">
                <div>
                    <p>Кран-балки</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->cranes[1]->cranes_cathead[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Шаг колон</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->options[1]->column_grid[1]) ?></p>

                </div>
            </td>
            <td class="three">
                <div>
                    <p>Тельферы</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->cranes[1]->telphers[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Грузовые лифты</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->options[1]->elevators[1]) ?></p>

                </div>
            </td>
            <td class="three">
            </td>
            <td class="four">
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            </td>
            <td class="two">
            </td>
            <td class="three">
                <div class="params-title">
                    <p>Инфраструктура</p>
                </div>
            </td>
            <td class="four">
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
            <div class="params-title">
                <p class="params-title">Безопасность</p>
            </div>
            </td>
            <td class="two">
            </td>
            <td class="three">
                <div>
                    <p>Въезд на территорию</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->infrastructure[1]->entry_territory[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Охрана объекта</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->security[1]->guard[1]) ?></p>

                </div>
            </td>
            <td class="three">
                <div>
                    <p>Парковка легковая</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->infrastructure[1]->parking_car[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Пожаротушение</p>

            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->security[1]->firefighting[1]) ?></p>

                </div>
            </td>
            <td class="three">
                <div>
                    <p>Парковка грузовая</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->infrastructure[1]->parking_truck[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Видеонаблюдение</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->security[1]->video_control[1]) ?></p>
                </div>
            </td>
            <td class="three">
                <div>
                    <p>Столовая/кафе</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->infrastructure[1]->canteen[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Контроль доступа</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->security[1]->access_control[1]) ?></p>

                </div>
            </td>
            <td class="three">
                <div>
                    <p>Общежитие</p>
                </div>
            </td>
            <td class="four">
                <div>
                    <p><?= $model->normalizeText($data->stats->infrastructure[1]->hostel[1]) ?></p>

                </div>
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Охранная сигнализация</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->security[1]->security_alert[1]) ?></p>

                </div>
            </td>
            <td class="three">
            </td>
            <td class="four">
            </td>
            </tr>
            </tbody>
        </table>
        <table class="params-new even"">
            <tbody>
                <tr>
                    <td class=" one">
            <div>
                <p>Пожарная сигнализация</p>
            </div>
            </td>
            <td class="two">
                <div>
                    <p><?= $model->normalizeText($data->stats->security[1]->fire_alert[1]) ?></p>

                </div>
            </td>
            <td class="three">
            </td>
            <td class="four">
            </td>
            </tr>
            </tbody>
        </table>
    </div> -->