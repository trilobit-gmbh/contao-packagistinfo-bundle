<?php

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-packagistinfo-bundle
 */

use Trilobit\PackagistinfoBundle\DataContainer\Edit;

$GLOBALS['TL_DCA']['tl_module']['palettes']['packagistinfocharts'] = '{title_legend},name,headline,type;{config_legend},packagistbundles,packagistdatatype,packagistcurrent;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['packagistinfotable'] = '{title_legend},name,headline,type;{config_legend},packagistbundles,packagistdatatype,packagistsummary;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['packagistbundles'] = [
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkboxWizard',
    'options' => Edit::onGroupOptions(),
    'eval' => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'clr w50'],
    'sql' => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['packagistsummary'] = [
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'clr w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['packagistcurrent'] = [
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr w50'],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['packagistdatatype'] = [
    'exclude' => true,
    'filter' => true,
    'inputType' => 'radio',
    'options' => ['downloads', 'favers', 'downloads-favers'],
    'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(32) NOT NULL default ''",
];
