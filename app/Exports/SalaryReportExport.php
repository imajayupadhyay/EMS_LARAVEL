<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalaryReportExport implements FromView
{
    protected $month, $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function view(): View
    {
        $controller = app(\App\Http\Controllers\Admin\SalaryReportController::class);
        $request = new \Illuminate\Http\Request([
            'month' => $this->month,
            'year' => $this->year
        ]);
        $data = $controller->index($request)->getData()['props'];

        return view('exports.salary_report', [
            'report' => $data['report'],
            'officeDays' => $data['officeDays']
        ]);
    }
}

