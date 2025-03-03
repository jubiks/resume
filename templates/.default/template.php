<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CAllMain $APPLICATION */
/** @var array $arParams */
/** @var array $arResult */
/** @var \CBitrixComponentTemplate $this */

use Bitrix\Main\Localization\Loc;

$this->addExternalCss("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css");
$this->addExternalCss($this->GetFolder() . "/css/slick.css");
$this->addExternalCss($this->GetFolder() . "/css/slick-theme.css");
$this->addExternalJs($this->GetFolder() . "/js/slick.min.js");
?>

<? if (!empty($arResult)): ?>
    <div class="container" id="resume-page">
        <aside class="sidebar">
            <div class="sticky-top flex">
                <?if($arResult['PICTURE']['ID']):?>
                <img
                    src="<?=$arResult['PICTURE']['SRC']?>"
                    alt="<?=$arResult['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']?>"
                    title="<?=$arResult['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']?>"
                    class="photo"
                />
                <?endif?>
                <ul class="contacts flex-grow">
                    <?if(!empty($arResult['PHONE'])):?>
                    <li id="phoneContainer">
                        <i class="fas fa-phone"></i>
                        <a id="phoneDisplay" class="phone-hidden">+7 (***) ***-**-**</a>
                    </li>
                    <?endif?>
                    <?if(!empty($arResult['EMAIL'])):?>
                    <li><i class="fas fa-envelope"></i><a href="mailto:<?=$arResult['EMAIL']?>"><?=$arResult['EMAIL']?></a></li>
                    <?endif?>
                    <?if(!empty($arResult['TELEGRAM'])):?>
                    <li><i class="fab fa-telegram"></i><a href="<?=$arResult['TELEGRAM']['URL']?>" target="_blank"><?=$arResult['TELEGRAM']['TEXT']?></a></li>
                    <?endif?>
                    <?if(!empty($arResult['LINKEDIN'])):?>
                    <li><i class="fab fa-linkedin"></i><a href="<?=$arResult['LINKEDIN']['URL']?>" target="_blank"><?=$arResult['LINKEDIN']['TEXT']?></a></li>
                    <?endif?>
                    <?if(!empty($arResult['GITHUB'])):?>
                    <li><i class="fab fa-github"></i><a href="<?=$arResult['GITHUB']['URL']?>" target="_blank"><?=$arResult['GITHUB']['TEXT']?></a></li>
                    <?endif?>
                    <?if(!empty($arResult['CITY'])):?>
                    <li><i class="fas fa-map-marker-alt"></i><?=$arResult['CITY']?></li>
                    <?endif?>
                </ul>
                <div class="actions">
                    <button class="print" title="<?=Loc::getMessage("PRINT_TITLE")?>" onclick="window.print();"><i class="fas fa-print"></i></button>
                    <!--button class="download" title="Скачать PDF" onclick="downloadPDF();"><i class="fas fa-download"></i></button-->
                </div>
            </div>
        </aside>

        <main class="main-content">
            <h1><?=$arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']?></h1>

            <section class="about">
                <h2><?=Loc::getMessage("ABOUT_TITLE")?></h2>
                <div class="description-text"><?=$arResult['DESCRIPTION']?></div>
            </section>

            <?if($arResult['CERTIFICATES']):?>
            <section class="certificates">
                <h2><?=Loc::getMessage("CERTIFICATES_TITLE")?></h2>
                <div class="certificates-list">
                    <?foreach ($arResult['CERTIFICATES'] as $certificate):?>
                    <div class="certificate-item"><img src="<?=$certificate['SRC']?>" alt="<?=$certificate['DESCRIPTION']?>" title="<?=$certificate['DESCRIPTION']?>" height="300" width="auto" class="certificate-image"></div>
                    <?endforeach?>
                </div>
            </section>
            <?endif?>

            <?if($arResult['SKILLS']):?>
            <section class="skills">
                <h2><?=Loc::getMessage("SKILLS_TITLE")?></h2>
                <ul class="skills-list">
                    <?foreach ($arResult['SKILLS'] as $skill):?>
                    <li><?=$skill?></li>
                    <?endforeach?>
                </ul>
            </section>
            <?endif?>

            <?if($arResult['EXPERIENCE']):?>
            <section class="experience">
                <h2><?=Loc::getMessage("EXPERIENCE_TITLE")?></h2>
                <?foreach ($arResult['EXPERIENCE'] as $experience):?>
                <div class="experience-item">
                    <h3><?=$experience['COMPANY_NAME']?></h3>
                    <div class="working-position"><strong><?=$experience['POSITION']?></strong> | <?=$experience['WORKING_PERIOD']?><?=$experience['WORKING_TIME'] ? " ({$experience['WORKING_TIME']})" : ""?></div>
                    <div class="description-text"><?=$experience['POSITION_DESCRIPTION']?></div>
                </div>
                <?endforeach?>
            </section>
            <?endif?>

            <?if($arResult['PORTFOLIO']):?>
            <section class="portfolio">
                <h2><?=Loc::getMessage("PORTFOLIO_TITLE")?></h2>
                <?foreach ($arResult['PORTFOLIO'] as $portfolio):?>
                <div class="project-item">
                    <?if($portfolio['TITLE']):?>
                        <h3><?=$portfolio['TITLE']?></h3>
                    <?endif?>
                    <div class="project-title">
                        <?=implode(' | ', array_map(function($item) {
                            return "<a href='{$item['URL']}' target='_blank'>{$item['TEXT']}</a>";
                        }, $portfolio['URL']));?>&nbsp;&ndash;&nbsp;<?=implode(', ', $portfolio['DEVELOP_TYPE'])?>
                    </div>
                    <div class="description-text"><?=$portfolio['DESCRIPTION']?></div>
                    <div class="technology-stack"><?=Loc::getMessage("TECHNOLOGY_STACK")?>: <?=implode(' ', array_map(function($value) {
                            return "<span>{$value}</span>";
                        }, $portfolio['TECHNOLOGY_STACK']))?></div>
                </div>
                <?endforeach?>
            </section>
            <?endif?>
            <a href="https://github.com/jubiks/resume" class="copyright" target="_blank"><i class="fab fa-github"></i>&nbsp;<?=Loc::getMessage("COPYRIGHT_TEXT")?></a>
        </main>
    </div>
<? endif ?>

<script>
    BX.message({
        RESUME_ID: '<?=(int)$arResult["ID"]?>',
    });
</script>
