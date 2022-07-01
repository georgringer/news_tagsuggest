<?php

$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['tags']['config']['type'] = 'group';
$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['tags']['config']['internal_type'] = 'db';
$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['tags']['config']['allowed'] = 'tx_news_domain_model_tag';
unset($GLOBALS['TCA']['tx_news_domain_model_news']['columns']['tags']['config']['renderType']);

$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['tags']['config']['suggestOptions'] = [
    'default' => [
        'minimumCharacters' => 2,
        'searchWholePhrase' => true,
        'receiverClass' => \GeorgRinger\NewsTagsuggest\Backend\Wizard\SuggestWizardReceiver::class
    ],
];
