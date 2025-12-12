<?php

namespace App\Exports;

use App\Models\Donasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DonasiSummaryExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Donasi::query()
            ->when($this->startDate, function($query) {
                return $query->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function($query) {
                return $query->whereDate('created_at', '<=', $this->endDate);
            });

        // Statistik berdasarkan status
        $stats = [
            [
                'Kategori' => 'Total Donasi',
                'Jumlah' => $query->count(),
                'Nominal' => 'Rp ' . number_format($query->sum('jumlah'), 0, ',', '.'),
            ],
            [
                'Kategori' => 'Donasi Sukses',
                'Jumlah' => $query->where('status', 'success')->count(),
                'Nominal' => 'Rp ' . number_format($query->where('status', 'success')->sum('jumlah'), 0, ',', '.'),
            ],
            [
                'Kategori' => 'Donasi Pending',
                'Jumlah' => $query->where('status', 'pending')->count(),
                'Nominal' => 'Rp ' . number_format($query->where('status', 'pending')->sum('jumlah'), 0, ',', '.'),
            ],
            [
                'Kategori' => 'Donasi Gagal',
                'Jumlah' => $query->where('status', 'failed')->count(),
                'Nominal' => 'Rp ' . number_format($query->where('status', 'failed')->sum('jumlah'), 0, ',', '.'),
            ],
            [
                'Kategori' => 'Donasi Kadaluarsa',
                'Jumlah' => $query->where('status', 'expired')->count(),
                'Nominal' => 'Rp ' . number_format($query->where('status', 'expired')->sum('jumlah'), 0, ',', '.'),
            ],
        ];

        // Tambahkan statistik per metode pembayaran
        $paymentMethods = Donasi::select('metode_pembayaran')
            ->selectRaw('COUNT(*) as jumlah')
            ->selectRaw('SUM(jumlah) as total')
            ->when($this->startDate, function($query) {
                return $query->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function($query) {
                return $query->whereDate('created_at', '<=', $this->endDate);
            })
            ->where('status', 'success')
            ->groupBy('metode_pembayaran')
            ->get();

        foreach ($paymentMethods as $method) {
            $stats[] = [
                'Kategori' => 'Metode ' . ucfirst($method->metode_pembayaran),
                'Jumlah' => $method->jumlah,
                'Nominal' => 'Rp ' . number_format($method->total, 0, ',', '.'),
            ];
        }

        return collect($stats);
    }

    public function headings(): array
    {
        return [
            'Kategori',
            'Jumlah Transaksi',
            'Total Nominal'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E75B5']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);

        // Style untuk total
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A{$lastRow}:C{$lastRow}")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2']
            ]
        ]);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(25);

        // Set row height untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [
            1 => ['font' => ['bold' => true]],
            $lastRow => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Donasi';
    }
}