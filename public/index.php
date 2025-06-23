<?php
    declare(strict_types=1);

    $root = dirname(__DIR__).DIRECTORY_SEPARATOR;

    define('APP_PATH', $root.'app'.DIRECTORY_SEPARATOR);
    define('BUDGETS_PATH', $root.'budget'.DIRECTORY_SEPARATOR);
    define('VIEWS_PATH', $root.'views'.DIRECTORY_SEPARATOR);

    include_once APP_PATH.'logic.php';
    include_once APP_PATH.'formatters.php';

    readBudgetFiles(BUDGETS_PATH);

    $transactions = [];
    foreach (readBudgetFiles(BUDGETS_PATH) as $budgetFile) {
        $transactions = array_merge($transactions, parseCsvFile($budgetFile, 'processTransaction'));
    }

    $totals = calculateTotals($transactions);

    include_once VIEWS_PATH.'views.php';