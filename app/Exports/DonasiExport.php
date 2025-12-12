<?php

namespace App\Exports;

use App\Models\Donasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DonasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Donasi::with(['kampanye', 'user'])
            ->when($this->status, function($query) {
                return $query->where('status', $this->status);
            })
            ->when($this->startDate, function($query) {
                return $query->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function($query) {
                return $query->whereDate('created_at', '<=', $this->endDate);
            })
            ->orderBy('created_at', 'desc');

        $donasis = $query->get();
        
        // Jika tidak ada data, return collection kosong dengan pesan
        if ($donasis->isEmpty()) {
            return collect([[
                'message' => 'Tidak ada data donasi',
                'empty' => true
            ]]);
        }
        
        return $donasis;
    }

    public function headings(): array
    {
        return [
            'Kode Donasi',
            'Nama Donatur',
            'Email',
            'Nomor Telepon',
            'Kampanye',
            'Jumlah Donasi',
            'Metode Pembayaran',
            'Status',
            'Tanggal Donasi',
            'Catatan',
            'Bukti Pembayaran',
            'Dibuat Pada'
        ];
    }

    public function map($donasi): array
    {
        // Handle jika data kosong
        if (isset($donasi['empty']) && $donasi['empty'] === true) {
            return array_fill(0, 12, 'Tidak ada data');
        }
        
        return [
            $donasi->kode_donasi,
            $donasi->nama_donatur,
            $donasi->email,
            $donasi->no_telepon,
            $donasi->kampanye ? $donasi->kampanye->judul : '-',
            'Rp ' . number_format($donasi->jumlah, 0, ',', '.'),
            $donasi->metode_pembayaran,
            $this->getStatusText($donasi->status),
            Carbon::parse($donasi->tanggal_donasi)->format('d/m/Y'),
            $donasi->catatan ?? '-',
            $donasi->bukti_pembayaran ? 'Ada' : 'Tidak Ada',
            Carbon::parse($donasi->created_at)->format('d/m/Y H:i:s')
        ];
    }

    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'Menunggu',
            'success' => 'Sukses',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa'
        ];
        
        return $statuses[$status] ?? $status;
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = $this->collection()->count();
        
        // Jika hanya ada 1 baris dan itu pesan "tidak ada data"
        if ($dataCount === 1 && isset($this->collection()->first()['empty'])) {
            $sheet->mergeCells('A1:L1');
            $sheet->setCellValue('A1', 'TIDAK ADA DATA DONASI');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FF0000'],
                    'size' => 14
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFFCC']
                ]
            ]);
            
            $sheet->getRowDimension(1)->setRowHeight(40);
            
            return [];
        }
        
        // Style untuk header (jika ada data)
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);

        // Auto filter jika ada data
        if ($dataCount > 0) {
            $sheet->setAutoFilter('A1:L' . ($dataCount + 1));
        }

        // Style untuk kolom jumlah (currency)
        $sheet->getStyle('F2:F' . ($dataCount + 1))
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0');

        // Wrap text untuk kolom catatan
        $sheet->getStyle('J2:J' . ($dataCount + 1))
            ->getAlignment()
            ->setWrapText(true);

        // Set row height untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Kode Donasi
            'B' => 20, // Nama Donatur
            'C' => 25, // Email
            'D' => 15, // Nomor Telepon
            'E' => 30, // Kampanye
            'F' => 15, // Jumlah Donasi
            'G' => 20, // Metode Pembayaran
            'H' => 12, // Status
            'I' => 15, // Tanggal Donasi
            'J' => 30, // Catatan
            'K' => 15, // Bukti Pembayaran
            'L' => 20, // Dibuat Pada
        ];
    }

    public function title(): string
    {
        return 'Data Donasi';
    }
}