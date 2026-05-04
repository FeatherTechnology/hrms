<?php
class OpeningDateClass
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function getOpeningDate($user_id)
    {
        $latestDateQry = $this->db->query(" SELECT MAX(latest_date) AS latest_transaction_date FROM (
                SELECT MAX(DATE(created_on)) AS latest_date FROM accounts_collect_entry WHERE insert_login_id = '$user_id'
                UNION ALL
                SELECT MAX(DATE(created_date)) AS latest_date FROM accounts_loan_issued WHERE insert_login_id = '$user_id'
                UNION ALL
                SELECT MAX(DATE(created_on)) AS latest_date FROM expenses WHERE insert_login_id = '$user_id'
                UNION ALL
                SELECT MAX(DATE(created_on)) AS latest_date FROM other_transaction WHERE insert_login_id = '$user_id'
                
            ) AS AllTransactionDates
        ");

        $latestTxnDate = $latestDateQry->fetch()['latest_transaction_date'];
        return $latestTxnDate;
    }
}
