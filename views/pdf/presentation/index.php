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
                                    Объект <?= $model->data->object_id ?>
                                </div>
                                <div class="content">
                                    <p class="district"><?= $model->data->district_name ?></p>
                                    <p class="type"><?= $model->data->object_type_name ?></p>
                                    <div class="items">
                                        <span class="btn-fake"><?= $model->data->town_name ?></span>
                                        <span class="btn-fake"><?= $model->data->highway_name ?></span>
                                        <span class="btn-fake"><?= $model->data->from_mkad ?> от МКАД</span>
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
                                            <p class="big"><?= $model->getArea() ?> м<sup>2</sup></p>
                                            <p class="small">Деление не предполагается</p>
                                        </div>
                                    </td>
                                    <td class="two">
                                        <div>
                                            <p><?= $model->getPriceLabel() ?><span class="danger"> <?= $model->data->tax_form ?> </span></p>
                                            <p class="big"><span class="danger"><?= $model->getPrice() ?> руб.</span></p>
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
                                                                    <p><?= $model->data->calc_floors ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="item">
                                                            <div>
                                                                <div class="icon">
                                                                    <img src="http://<?= $model->getHost() ?>/images/gates-icon.png" alt="">
                                                                    <p><?= $model->getGatesCount() ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="item">
                                                            <div>
                                                                <div class="icon">
                                                                    <img src="http://<?= $model->getHost() ?>/images/power-icon.png" alt="">
                                                                    <p><?= $model->getPower() ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="item">
                                                            <div>
                                                                <div class="icon">
                                                                    <img src="http://<?= $model->getHost() ?>/images/ceiling-icon.png" alt="">
                                                                    <p><?= $model->data->calc_ceilingHeight ?></p>

                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="item">
                                                            <div>
                                                                <div class="icon">
                                                                    <img src="http://<?= $model->getHost() ?>/images/floor-icon.png" alt="">
                                                                    <p><?= $model->data->self_leveling ? 'Антипыль' : 'нет' ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="item">
                                                            <div>
                                                                <div class="icon">
                                                                    <img src="http://<?= $model->getHost() ?>/images/crane-icon.png" alt="">
                                                                    <p><?= 2 ?></p>

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
    </div>