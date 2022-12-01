<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use DB;

use App\Application;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DailyReportExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize, WithColumnFormatting
{
    use Exportable;
    private $date;

    public function __construct(string $date, string $branch)
    {
        $this->date = $date;
        $this->branch = $branch;
    }

    public function collection()
    {
        return Application::leftJoin('partner_companies', 'applications.customer_company', '=', 'partner_companies.id')
                            ->leftJoin('users', 'applications.encoded_by', '=', 'users.username')
                            ->leftJoin('branches', 'applications.branch', '=', 'branches.code')
                            ->leftJoin('visa_types', 'applications.visa_type', '=', 'visa_types.id')
                            ->whereRaw("DATE_FORMAT(applications.application_date, '%Y-%m-%d') = '".$this->date."' AND (applications.payment_status = 'PAID' OR (applications.payment_status = 'UNPAID' AND applications.customer_type = 'PIATA')) AND applications.branch = '".$this->branch."'")
                            ->orderBy('applications.application_date', 'ASC')
                            ->select('applications.reference_no',
                                      DB::raw("CONCAT(applications.lastname, ', ', applications.firstname, ' ', applications.middlename) AS fullname"),
                                      'partner_companies.name',
                                      DB::raw("users.name as username"),
                                      DB::raw("MONTHNAME(applications.application_date)"),
                                      DB::raw("DATE_FORMAT(applications.application_date, '%Y')"),
                                      DB::raw("DATE_FORMAT(applications.application_date, '%d-%m-%Y %H:%i:%s')"),
                                      'branches.description',
                                      DB::raw("visa_types.name as visaname"),
                                      'applications.customer_type',
                                      'applications.passport_no',
                                      'applications.payment_mode',
                                      DB::raw("IF(IFNULL(applications.or_number, 'ACKNOWLEDGEMENT') = 'ACKNOWLEDGEMENT', 'ACKNOWLEDGEMENT', 'OR')"),
                                      'visa_types.price',
                                      DB::raw("visa_types.price - applications.visa_price"),
                                      DB::raw('visa_types.price * 0'),
                                      DB::raw('(visa_types.price / 1.12) * 0.12'),
                                      'applications.visa_price'
                                    )
                            ->get();
    }

    public function headings(): array
    {
        return [
            "Reference No.",
            "Applicant",
            "Group Name",
            "Filer Name",
            "Month",
            "Year",
            "Application Date",
            "Area",
            "Visa Type",
            "Application Type",
            "PassportNo",
            "PaymentMode",
            "PaymentRequest",
            "Visa Fee",
            "Discount",
            "Handling Fee",
            "VAt",
            "Grand Total",
        ];
    }

    public function registerEvents(): array
    {
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });

        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->styleCells('A1:R1', [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $event->sheet->getStyle('A1:R1')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ]
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFDEDEDE',
                        ],
                    ]
                ]);
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'N' => NumberFormat::FORMAT_NUMBER_00,
            'O' => NumberFormat::FORMAT_NUMBER_00,
            'P' => NumberFormat::FORMAT_NUMBER_00,
            'Q' => NumberFormat::FORMAT_NUMBER_00,
            'R' => NumberFormat::FORMAT_NUMBER_00
        ];
    }
}
