<?php
    #define multiple functions which will read all files within budge directory and process them.
    declare(strict_types=1);

    #function to read all csv files
    function readBudgetFiles(string $path): array {

        $filesHolder = [];
        //Removes dots files if on linux ubuntu paths
        $scanned_files = array_diff(scandir($path), array('..', '.'));
        foreach($scanned_files as $file) {
            if (is_file($path . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                $filesHolder[] = $path.$file;
            }
        }

        return $filesHolder;
    }

    #function to parse csv files into an array
    function parseCsvFile(string $filePath, ?callable $transactionAction = null): array {
        $transactionHolder = [];
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            fgetcsv($handle, 10000, ",");
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    if($transactionAction !== null) {
                        $transactionHolder[] = $transactionAction($data);
                    }else {
                        $transactionHolder[] = $data;
                    }
                }
            }
            fclose($handle); 
        }

        return $transactionHolder;
    }

    #function to process transactions
    function processTransaction(array $transactionRow): array{
        [$transactionDate, $transactionCheck, $transactionDesc, $transactionAmount] = $transactionRow;

        $transactionAmount = (float) str_replace(['$', ','], '', $transactionAmount);

        return [
            'transDate' => $transactionDate,
            'transCheckNr' => $transactionCheck,
            'transDesc' => $transactionDesc,
            'transAmount' => $transactionAmount
        ];
    }

    #function to calculate the total amount of all transactions
    function calculateTotals(array $transactions): array{
        $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

        foreach ($transactions as $transaction) {
            $totals['netTotal'] += $transaction['transAmount'];

            if ($transaction['transAmount'] >= 0) {
                $totals['totalIncome'] += $transaction['transAmount'];
            } else {
                $totals['totalExpense'] += $transaction['transAmount'];
            }
        }

        return $totals;
    }