<?php


namespace App\Calendar\Controller;


use Base\Controller\BasicController;
use Service\Balance\BalanceService;
use Service\Balance\Model\BalanceModel;
use Service\Balance\Model\TransactionModel;
use Service\Calendar\CalendarService;
use Service\Plan\Model\PlanModel;
use Service\Plan\PlansService;
use Service\User\Model\UserModel;

class Calendar extends BasicController
{
    /**
     * Year, human-readable eg 2018
     * @var int
     */
    private $yearNumber;

    /**
     * Month number, human-readable eg 1
     * @var int
     */
    private $monthNumber;

    /**
     * Month start unix time eg 1514754000
     *
     * @var int
     */
    private $month;

    public function prepare()
    {
        $this->yearNumber = $this->p('year');
        $this->monthNumber = $this->p('month');

        if ($this->yearNumber) {
            $this->setState('year', $this->yearNumber);
        } else {
            $this->yearNumber = $this->getState('year', (int) date('Y'));
        }

        if ($this->monthNumber) {
            $this->setState('month', $this->monthNumber);
        } else {
            $this->monthNumber = $this->getState('month', (int) date('m'));
        }

        $monthDate = new \DateTime('', new \DateTimeZone($this->_user[UserModel::TIMEZONE] ?: 'UTC'));
        $monthDate->setDate($this->yearNumber, $this->monthNumber, 1);
        $monthDate->setTime(0,0,0);
        
        $this->month = $monthDate->getTimestamp();
    }

    public function index()
    {
        $calendar = new CalendarService();
        $timezone = new \DateTimeZone($this->_user[UserModel::TIMEZONE] ?: 'UTC');
        $days = $calendar->getDaysOfMonthDate($this->month, $this->_user[UserModel::TIMEZONE]);

        $plans = new PlansService();
        $minDay = min($days);
        $maxDay = max($days) + CalendarService::DAY_SECONDS;

        $plans = $plans->getPlans($this->_budgetId, $minDay, $maxDay);

        $daysPlans = [];
        foreach ($days as $day) {
            $daysPlans[$day]['plans'] = [];
        }

        $message = '';
        foreach ($plans as $plan) {
            $dayTime = new \DateTime('', $timezone);
            $dayTime->setTimestamp($plan[PlanModel::DATE]);
            $dayTime->setTime(0, 0, 0);
            $day = $dayTime->getTimestamp();
            
            if (!isset($daysPlans[$day])) {
                $message .= ', Suggestion ' . date('c') . 'not found a day';
                continue;
            }

            $daysPlans[$day]['plans'][] = $plan;
        }

        $balanceService = new BalanceService();
        $balances = $balanceService->getBudgetBalances($this->_budgetId);
        $balancesIds = array_column($balances, BalanceModel::ID);
        $transactions = $balanceService->getBalancesTransactions($balancesIds, $minDay, $maxDay);
        
        foreach ($transactions as $transaction) {
            $dayTime = new \DateTime('', $timezone);
            $dayTime->setTimestamp($transaction[TransactionModel::CREATED_DATE]);
            $dayTime->setTimezone($timezone);
            $dayTime->setTime(0, 0, 0);
            $day = $dayTime->getTimestamp();
            if (!isset($daysPlans[$day])) {
                $message .= 'Transactions ' . date('c', $transaction[TransactionModel::CREATED_DATE]) . 'not found a day';
                continue;
            }

            $daysPlans[$day]['transactions'][] = $transaction;
        }
        
        return $this->_render('month', [
            'month'     => $this->month,
            'year'      => $this->yearNumber,
            'days'      => $days,
            'daysPlans' => $daysPlans,
            'message'   => $message,
            'balances'  => $balances,
        ]);
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}