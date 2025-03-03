<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Iblock\Elements\ElementResumeTable as Resume;
use Bitrix\Iblock\Elements\ElementPortfolioTable as PortfolioTable;
use Bitrix\Iblock\Elements\ElementExperienceTable as Experience;

class ResumeComponent extends CBitrixComponent implements Controllerable
{
    /** @var ErrorCollection $errors Errors. */
    protected $errors;
    var $arResult = [];

    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->errors = new ErrorCollection();
        Loader::includeModule("iblock");
        CJSCore::Init(array("jquery"));
    }

    protected function addError($message, $code = '')
    {
        $this->errors->setError(new \Bitrix\Main\Error($message, $code));
    }

    protected function printErrors()
    {
        foreach ($this->errors->toArray() as $error)
        {
            \ShowError($error->getMessage());
        }
    }

    protected function checkRequiredParams()
    {
        if(!$this->arParams['RESUME_ID']) {
            $this->addError('No resume ID');
            return false;
        }
        return true;
    }

    protected function initParams()
    {
        $this->arParams["RESUME_ID"] = (int)$this->arParams["RESUME_ID"] ?: null;
    }

    protected function prepareResult()
    {
        return true;
    }

    public function GetData() {
        $dbElements = Resume::getList([
            'select' => [
                'ID',
                'IBLOCK_ID',
                'NAME',
                'PREVIEW_PICTURE',
                'PREVIEW_TEXT',
                'PREVIEW_TEXT_TYPE',
                'SPECIAL_NAME',
                'BIRTHDAY',
                'PHONE',
                'EMAIL',
                'TELEGRAM',
                'LINKEDIN',
                'GITHUB',
                'CITY',
                'SKILLS',
                'CERTIFICATES',
                'EXPERIENCE',
                'PORTFOLIO',
            ],
            'filter' => [
                '=ID' => $this->arParams['RESUME_ID'],
                '=ACTIVE' => 'Y'
            ],
            'cache' => [
                'ttl' => 86400,
                'cache_joins' => true
            ]
        ]);

        if ($obResume = $dbElements->fetchObject()) {
            //d($obResume);

            $this->arResult = [
                'ID' => $obResume->Get('ID'),
                'NAME' => $obResume->Get('NAME'),
                'PICTURE' => CFile::GetFileArray((int)$obResume->Get('PREVIEW_PICTURE')),
                'DESCRIPTION' => $obResume->Get('PREVIEW_TEXT_TYPE') == 'text' ? nl2br($obResume->Get('PREVIEW_TEXT')) : $obResume->Get('PREVIEW_TEXT'),
                'SPECIAL_NAME' => $obResume->Get('SPECIAL_NAME')->getValue(),
                'BIRTHDAY' => $obResume->Get('BIRTHDAY')->getValue(),
                'PHONE' => $obResume->Get('PHONE')->getValue(),
                'EMAIL' => $obResume->Get('EMAIL')->getValue(),
                'TELEGRAM' => [
                    'URL' => $obResume->Get('TELEGRAM')->getValue(),
                    'TEXT' => $obResume->Get('TELEGRAM')->getDescription() ?: $obResume->Get('TELEGRAM')->getValue()
                ],
                'LINKEDIN' => [
                    'URL' => $obResume->Get('LINKEDIN')->getValue(),
                    'TEXT' => $obResume->Get('LINKEDIN')->getDescription() ?: $obResume->Get('LINKEDIN')->getValue()
                ],
                'GITHUB' => [
                    'URL' => $obResume->Get('GITHUB')->getValue(),
                    'TEXT' => $obResume->Get('GITHUB')->getDescription() ?: $obResume->Get('GITHUB')->getValue()
                ],
                'CITY' => $obResume->Get('CITY')->getValue(),
                'SKILLS' => array_map(static function($skill) { return $skill->getValue(); }, $obResume->Get('SKILLS')->getAll()),
                'CERTIFICATES' => array_map(static function($file) {
                    return CFile::GetFileArray($file->getValue());
                }, $obResume->Get('CERTIFICATES')->getAll()),
                'EXPERIENCE' => array_map(static function($experience) {
                    $dbExperience = Experience::getList([
                        'select' => [
                            'ID',
                            'SORT',
                            'NAME',
                            'PREVIEW_TEXT',
                            'PREVIEW_TEXT_TYPE',
                            'DETAIL_TEXT',
                            'DETAIL_TEXT_TYPE',
                            'WORKING_PERIOD',
                            'WORKING_TIME',
                            'POSITION',
                        ],
                        'filter' => [
                            '=ID' => $experience->getValue(),
                            '=ACTIVE' => 'Y'
                        ],
                        'cache' => [
                            'ttl' => 86400,
                            'cache_joins' => true
                        ]
                    ]);

                    if ($obExperience = $dbExperience->fetchObject()) {
                        return [
                            'ID' => $obExperience->getId(),
                            'SORT' => $obExperience->getSort(),
                            'COMPANY_NAME' => $obExperience->getName(),
                            'COMPANY_DESCRIPTION' => $obExperience->getPreviewTextType() == 'text' ? nl2br($obExperience->getPreviewText()) : $obExperience->getPreviewText(),
                            'WORKING_PERIOD' => $obExperience->Get('WORKING_PERIOD') ? $obExperience->Get('WORKING_PERIOD')->getValue() : null,
                            'WORKING_TIME' => $obExperience->Get('WORKING_TIME') ? $obExperience->Get('WORKING_TIME')->getValue() : null,
                            'POSITION' => $obExperience->Get('POSITION') ? $obExperience->Get('POSITION')->getValue() : null,
                            'POSITION_DESCRIPTION' => $obExperience->getDetailTextType() == 'text' ? nl2br($obExperience->getDetailText()) : $obExperience->getDetailText(),
                        ];
                    }

                    return null;
                }, $obResume->Get('EXPERIENCE')->getAll()),
                'PORTFOLIO' => array_map(static function($portfolio) {
                    $dbPortfolio = PortfolioTable::getList([
                        'select' => [
                            'ID',
                            'SORT',
                            'NAME',
                            'PREVIEW_TEXT',
                            'PREVIEW_TEXT_TYPE',
                            'TITLE',
                            'URL',
                            'DEVELOP_TYPE.ITEM',
                            'TECHNOLOGY_STACK',
                        ],
                        'filter' => [
                            '=ID' => $portfolio->getValue(),
                            '=ACTIVE' => 'Y'
                        ],
                        'cache' => [
                            'ttl' => 86400,
                            'cache_joins' => true
                        ]
                    ]);

                    if ($obPortfolio = $dbPortfolio->fetchObject()) {
                        return [
                            'ID' => $obPortfolio->getId(),
                            'SORT' => $obPortfolio->getSort(),
                            'NAME' => $obPortfolio->getName(),
                            'TITLE' => $obPortfolio->getTitle() ? $obPortfolio->getTitle()->getValue() : null,
                            'DESCRIPTION' => $obPortfolio->getPreviewTextType() == 'text' ? nl2br($obPortfolio->getPreviewText()) : $obPortfolio->getPreviewText(),
                            'URL' => array_map(static function($url) {
                                return [
                                    'URL' => $url->getValue(),
                                    'TEXT' => $url->getDescription() ?: $url->getValue()
                                ];
                            }, $obPortfolio->Get('URL')->getAll()),
                            'DEVELOP_TYPE' => array_map(static function($type) { return $type->getItem()->getValue(); }, $obPortfolio->Get('DEVELOP_TYPE')->getAll()),
                            'TECHNOLOGY_STACK' => array_map(static function($stack) { return $stack->getValue(); }, $obPortfolio->Get('TECHNOLOGY_STACK')->getAll()),
                        ];
                    }

                    return null;
                }, $obResume->Get('PORTFOLIO')->getAll()),
            ];

            if($this->arResult['EXPERIENCE']) {
                $this->arResult['EXPERIENCE'] = array_filter($this->arResult['EXPERIENCE']);
                usort($this->arResult['EXPERIENCE'], static function ($a, $b) {
                    return (int)$b['SORT'] <=> (int)$a['SORT'];
                });
            }

            if($this->arResult['PORTFOLIO']) {
                $this->arResult['PORTFOLIO'] = array_filter($this->arResult['PORTFOLIO']);
                usort($this->arResult['PORTFOLIO'], static function ($a, $b) {
                    return (int)$a['SORT'] <=> (int)$b['SORT'];
                });
            }

            $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues(
                $obResume->getIblockId(), // ID инфоблока
                $obResume->getId() // ID элемента
            );
            $this->arResult['IPROPERTY_VALUES'] = $ipropValues->getValues();

        }

        return true;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $this->initParams();
/*
        if ($this->startResultCache($this->arParams['CACHE_TIME'])) {
            if (!$this->checkRequiredParams())
            {
                $this->AbortResultCache();
                $this->printErrors();
                return;
            }

            $this->GetData();

            if (!$this->prepareResult())
            {
                $this->AbortResultCache();
                $this->printErrors();
                return;
            }

            $this->IncludeComponentTemplate();
        }
*/
        $this->GetData();
        $this->IncludeComponentTemplate();

        if($this->arResult['ID'] && !empty($this->arResult['IPROPERTY_VALUES']['ELEMENT_META_TITLE'])) {
            $APPLICATION->SetTitle($this->arResult['IPROPERTY_VALUES']['ELEMENT_META_TITLE']);
            $APPLICATION->SetPageProperty("title", $this->arResult['IPROPERTY_VALUES']['ELEMENT_META_TITLE']);
        } elseif($this->arResult['ID']) {
            $APPLICATION->SetTitle($this->arResult['NAME']);
            $APPLICATION->SetPageProperty("title", $this->arResult['NAME']);
        }
    }

    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'getPhoneNumber' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                ],
                'postfilters' => []
            ],
        ];
    }

    public function getPhoneNumberAction(Int $id)
    {
        $dbElements = Resume::getList([
            'select' => [
                'ID',
                'PHONE',
            ],
            'filter' => [
                '=ID' => $id,
                '=ACTIVE' => 'Y'
            ],
            'cache' => [
                'ttl' => 86400,
                'cache_joins' => true
            ]
        ]);

        if ($obResume = $dbElements->fetchObject())
        {
            $phoneNumber = $obResume->getPhone()->getValue();
            return $phoneNumber ? base64_encode($phoneNumber) : null;
        }

        return null;
    }
}