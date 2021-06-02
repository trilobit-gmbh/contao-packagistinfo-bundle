<?php

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-packagistinfo-bundle
 */

use Trilobit\PackagistinfoBundle\DataContainer\Edit;

$GLOBALS['TL_DCA']['tl_module']['palettes']['packagistinfocharts'] = '{title_legend},name,headline,type;{config_legend},packagistbundles,packagistcurrent;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['packagistinfotable'] = '{title_legend},name,headline,type;{config_legend},packagistbundles,packagistcurrent,packagistsummary;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['packagistbundles'] = [
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkboxWizard',
    'options' => Edit::onGroupOptions(),
    'eval' => ['multiple' => true, 'feEditable' => true, 'feGroup' => 'login'],
    'sql' => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['packagistsummary'] = [
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['packagistcurrent'] = [
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'sql' => "char(1) NOT NULL default ''",
];
