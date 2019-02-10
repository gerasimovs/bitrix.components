<? if ($arResult['MAX_COUNT'] && $arResult['COUNT_IN_SECTION'] > 0): ?>
    <div class="catalog-sorting">
        <div class="catalog-sorting__dropdown">
            <button class="catalog-sorting__dropdown-btn" type="button" data-toggle="dropdown" data-target="#dropdownSorting">
                Cортировка товаров
            </button>
            <div class="catalog-sorting__dropdown-menu" id="dropdownSorting">
                <a class="catalog-sorting__dropdown-menu__item" href="<?= $APPLICATION->GetCurPageParam("sort=price", array('sort'), false) ?>">По цене</a>
                <a class="catalog-sorting__dropdown-menu__item" href="<?= $APPLICATION->GetCurPageParam("sort=popular", array('sort'), false) ?>">По популярности</a>
                <a class="catalog-sorting__dropdown-menu__item" href="<?= $APPLICATION->GetCurPageParam("sort=name", array('sort'), false) ?>">По названию</a>
                <a class="catalog-sorting__dropdown-menu__item" href="<?= $APPLICATION->GetCurPageParam("sort=new", array('sort'), false) ?>">Сначала новинки</a>
            </div>
        </div>
        <? if ($arResult['MAX_COUNT'] > $arResult['MIN_COUNT']): ?>
            <div class="catalog-sorting__dropdown">
                <button class="catalog-sorting__dropdown-btn" type="button" data-toggle="dropdown" data-target="#dropdownCount">
                    Количество на страницу
                </button>
                <div class="catalog-sorting__dropdown-menu" id="dropdownCount">

                    <? 
                    $iteration = 1; 
                    $count = $arResult['MIN_COUNT'];

                    while ($count <= $arResult['MAX_COUNT']): ?>
                        <a class="catalog-sorting__dropdown-menu__item" href="<?= $APPLICATION->GetCurPageParam("count={$count}", array('count'), false) ?>"><?= $count ?> товаров</a>
                        <?
                        $iteration++; 
                        $count = $iteration * $arResult['MIN_COUNT'];
                    endwhile; ?>

                    <? if ($arResult['SHOW_ALL'] && $arResult['COUNT_IN_SECTION'] > $count - $arResult['MIN_COUNT']): ?>
                        <a class="catalog-sorting__dropdown-menu__item" href="<?= $APPLICATION->GetCurPageParam("count=view_all", array('count'), false) ?>">Все товары</a>
                    <? endif; ?>
                </div>
            </div>
        <? endif; ?>
    </div>
<? endif; ?>