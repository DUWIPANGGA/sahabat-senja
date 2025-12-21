<?php

namespace App\Exports;

use App\Models\Datalansia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DataLansiaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function collection()
    {
        $query = Datalansia::query();
        
        // Apply filters
        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('nama_lansia', 'like', "%{$this->filters['search']}%")
                  ->orWhere('nama_anak', 'like', "%{$this->filters['search']}%")
                  ->orWhere('no_hp_anak', 'like', "%{$this->filters['search']}%")
                  ->orWhere('email_anak', 'like', "%{$this->filters['search']}%");
            });
        }
        
        if (!empty($this->filters['jenis_kelamin'])) {
            $query->where('jenis_kelamin_lansia', $this->filters['jenis_kelamin']);
        }
        
        // Apply sorting
        switch ($this->filters['sort'] ?? '') {
            case 'nama_asc':
                $query->orderBy('nama_lansia', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('nama_lansia', 'desc');
                break;
            case 'umur_asc':
                $query->orderBy('umur_lansia', 'asc');
                break;
            case 'umur_desc':
                $query->orderBy('umur_lansia', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        return $query->get();
    }
    
    public function headings(): array
    {
        return [
            'No',
            'Nama Lansia',
            'Umur',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Tempat Lahir',
            'Golongan Darah',
            'Riwayat Penyakit',
            'Alergi',
            'Obat Rutin',
            'Nama Anak',
            'No. HP Anak',
            'Email Anak',
            'Alamat Lengkap',
            'Tanggal Registrasi'
        ];
    }
    
    public function map($datalansia): array
    {
        return [
            $datalansia->id,
            $datalansia->nama_lansia,
            $datalansia->umur_lansia,
            $datalansia->jenis_kelamin_lansia,
            $datalansia->tanggal_lahir_lansia 
                ? Carbon::parse($datalansia->tanggal_lahir_lansia)->format('d/m/Y')
                : '-',
            $datalansia->tempat_lahir_lansia ?? '-',
            $datalansia->gol_darah_lansia ?? '-',
            $datalansia->riwayat_penyakit_lansia ?? '-',
            $datalansia->alergi_lansia ?? '-',
            $datalansia->obat_rutin_lansia ?? '-',
            $datalansia->nama_anak ?? '-',
            $datalansia->no_hp_anak ?? '-',
            $datalansia->email_anak ?? '-',
            $datalansia->alamat_lengkap ?? '-',
            $datalansia->created_at->format('d/m/Y H:i'),
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1    => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3498DB']
                ]
            ],
            
            // Style untuk semua cell
            'A:O' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }
}