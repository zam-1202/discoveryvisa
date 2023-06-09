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
use App\Branch;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DailyReportExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize, WithColumnFormatting
{
    use Exportable;
    private $date;
    private $branch;
    private $role;
    private $rowCount;

    public function __construct(string $date, string $branch, string $role)
    {
        $this->date = $date;
        $this->branch = $branch;
        $this->role = $role;

    }

    public function collection()
    {

        $applications = Application::leftJoin('partner_companies', 'applications.customer_company', '=', 'partner_companies.id')
                            ->leftJoin('users', 'applications.encoded_by', '=', 'users.username')
                            ->leftJoin('branches', 'applications.branch', '=', 'branches.code')
                            ->leftJoin('visa_types', 'applications.visa_type', '=', 'visa_types.id')
                            ->whereRaw("DATE_FORMAT(applications.application_date, '%Y-%m-%d') = '".$this->date."' AND (applications.payment_status = 'PAID' OR (applications.payment_status = 'UNPAID' AND applications.customer_type = 'PIATA'))")
                            ->orderBy('applications.application_date', 'ASC')
                            ->select('applications.reference_no',
                                      DB::raw("CONCAT(applications.lastname, ', ', applications.firstname, ' ', applications.middlename) AS fullname"),
                                      DB::raw("applications.group_name"),
                                      DB::raw("users.name as username"),
                                      DB::raw("MONTHNAME(applications.application_date)"),
                                      DB::raw("DATE_FORMAT(applications.application_date, '%Y')"),
                                      DB::raw("DATE_FORMAT(applications.application_date, '%d-%m-%Y %H:%i:%s')"),
                                      'branches.description',
                                      DB::raw("applications.visa_type"),
                                      'applications.customer_type',
                                      'applications.passport_no',
                                      'applications.payment_mode',
                                      DB::raw("IF(IFNULL(applications.or_number, 'ACKNOWLEDGEMENT') = 'ACKNOWLEDGEMENT', 'ACKNOWLEDGEMENT', 'OR')"),
                                      'applications.visa_price',
                                      DB::raw("visa_types.visa_fee - applications.visa_price"), //discount
                                      DB::raw('applications.handling_price'),
                                      DB::raw('(COALESCE(applications.visa_price, 0) + COALESCE(applications.handling_price, 0)) - ((COALESCE(applications.handling_price, 0) + COALESCE(applications.visa_price, 0)) / 1.12)'), // VAT
                                      DB::raw('(COALESCE(applications.visa_price, 0) + COALESCE(applications.handling_price, 0))'),
                                      'applications.visa_price'
                                    )
                            ->get();


        if ($this->role == 'Cashier') {
            $allApplication = collect($applications)->filter(function ($application){
                return substr($application->reference_no, 0, 3) == $this->branch;
            });

            $this->rowCount = collect($this->rowCount)->push(COUNT($allApplication));

            return $allApplication;
        } else {
            $branches = Branch::select('code')->orderBy('id', 'asc')->get();

            $allApplication = collect();

            for ($i=0; $i < COUNT($branches); $i++) {

                $temp = collect($applications)->filter(function ($application) use($branches, $i){
                    return substr($application->reference_no, 0, 3) == $branches[$i]->code;
                });

                $this->rowCount = collect($this->rowCount)->push(COUNT($temp));

                if ($i != '0') {
                    $allApplication = $allApplication->push(collect(['space1' => '']));
                    $allApplication = $allApplication->push(collect(['space2' => '']));
                    $allApplication = $allApplication->push(collect(['code' => $branches[$i]->code]));
                }

                $allApplication = $allApplication->push($temp);
            }

            return $allApplication;
        }


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
                    'font' => [
                        'bold' => true
                    ]
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

                $currentRow = 2;
                for ($i=0; $i < COUNT($this->rowCount); $i++) {
                    $event->sheet->getStyle('A' .$currentRow .':R' .($this->rowCount[$i] + $currentRow - 1))->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '00000000'],
                            ]
                        ],
                    ]);

                    if ($i != '0') {
                        $event->sheet->getStyle('A' .($currentRow - 1))->applyFromArray([
                            'font' => [
                                'bold' => true
                            ]
                        ]);
                    }

                    $currentRow = $currentRow + $this->rowCount[$i] + 3;
                }


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
