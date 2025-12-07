<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            // Emergency Templates
            [
                'template_code' => 'EMERGENCY_MEDICAL_CRITICAL',
                'name' => 'Darurat Medis Kritis',
                'subject' => '🚨 DARURAT: {lansia_name} dalam Kondisi Kritis',
                'content' => "Lansia {lansia_name} ({lansia_age} tahun) mengalami kondisi medis kritis:\n\n{condition}\n\nLokasi: {location}\nRumah Sakit: {hospital_name}\nKontak Darurat: {contact_person} - {contact_number}\n\nSegera berikan pertolongan pertama atau hubungi rumah sakit terdekat.",
                'variables' => json_encode(['lansia_name', 'lansia_age', 'condition', 'location', 'hospital_name', 'contact_person', 'contact_number']),
                'description' => 'Template untuk notifikasi darurat medis kritis',
                'type' => 'all',
                'category' => 'emergency',
                'target_audience' => 'family_members',
                'is_active' => true,
                'is_auto_send' => true
            ],
            [
                'template_code' => 'HOSPITALIZATION_ALERT',
                'name' => 'Notifikasi Dirujuk ke RS',
                'subject' => '🏥 {lansia_name} Dirujuk ke Rumah Sakit',
                'content' => "Lansia {lansia_name} telah dirujuk ke {hospital_name} karena:\n\n{reason}\n\nStatus saat ini: {status}\nDokter penanggung jawab: {doctor_name}\nNomor ruangan: {room_number}\n\nSilakan kunjungi atau hubungi rumah sakit untuk informasi lebih lanjut.",
                'variables' => json_encode(['lansia_name', 'hospital_name', 'reason', 'status', 'doctor_name', 'room_number']),
                'description' => 'Template untuk notifikasi ketika lansia dirujuk ke rumah sakit',
                'type' => 'all',
                'category' => 'emergency',
                'target_audience' => 'family_members',
                'is_active' => true,
                'is_auto_send' => true
            ],
            
            // Medical Templates
            [
                'template_code' => 'VITAL_SIGN_ALERT',
                'name' => 'Alert Tanda Vital Abnormal',
                'subject' => '⚠️ Tanda Vital {lansia_name} Abnormal',
                'content' => "Tanda vital lansia {lansia_name} menunjukkan nilai abnormal:\n\n{vital_signs}\n\nNilai normal: {normal_range}\nWaktu pemeriksaan: {check_time}\nPerawat yang memeriksa: {nurse_name}\n\nSegera periksa kondisi lansia.",
                'variables' => json_encode(['lansia_name', 'vital_signs', 'normal_range', 'check_time', 'nurse_name']),
                'description' => 'Template untuk notifikasi tanda vital abnormal',
                'type' => 'all',
                'category' => 'medical',
                'target_audience' => 'nurses',
                'is_active' => true,
                'is_auto_send' => true
            ],
            
            // Medication Templates
            [
                'template_code' => 'MEDICATION_REMINDER',
                'name' => 'Pengingat Minum Obat',
                'subject' => '💊 Waktu Minum Obat {lansia_name}',
                'content' => "Hai {family_name},\n\nIni pengingat bahwa lansia {lansia_name} harus minum obat:\n\nObat: {medication_name}\nDosis: {dosage}\nWaktu: {medication_time}\nCatatan: {notes}\n\nPastikan obat diminum tepat waktu.",
                'variables' => json_encode(['family_name', 'lansia_name', 'medication_name', 'dosage', 'medication_time', 'notes']),
                'description' => 'Template untuk pengingat minum obat',
                'type' => 'all',
                'category' => 'medication',
                'target_audience' => 'family_members',
                'is_active' => true,
                'is_auto_send' => true
            ],
            
            // Financial Templates
            [
                'template_code' => 'PAYMENT_REMINDER',
                'name' => 'Pengingat Pembayaran Iuran',
                'subject' => '💰 Pengingat Pembayaran Iuran {period}',
                'content' => "Hai {user_name},\n\nIni pengingat pembayaran iuran untuk periode {period}.\n\nDetail:\nNama Iuran: {iuran_name}\nJumlah: Rp {amount}\nJatuh Tempo: {due_date}\n\nSilakan lakukan pembayaran sebelum tanggal jatuh tempo.\n\nTerima kasih.",
                'variables' => json_encode(['user_name', 'period', 'iuran_name', 'amount', 'due_date']),
                'description' => 'Template untuk pengingat pembayaran iuran',
                'type' => 'all',
                'category' => 'financial',
                'target_audience' => 'family_members',
                'is_active' => true,
                'is_auto_send' => true
            ],
            
            // System Templates
            [
                'template_code' => 'SYSTEM_MAINTENANCE',
                'name' => 'Pemberitahuan Maintenance Sistem',
                'subject' => '🔧 Maintenance Sistem {system_name}',
                'content' => "Pemberitahuan Maintenance Sistem\n\nSistem {system_name} akan dilakukan maintenance pada:\n\nTanggal: {maintenance_date}\nWaktu: {maintenance_time}\nDurasi: {duration}\n\nSelama maintenance, sistem mungkin tidak dapat diakses.\n\nTerima kasih atas pengertiannya.",
                'variables' => json_encode(['system_name', 'maintenance_date', 'maintenance_time', 'duration']),
                'description' => 'Template untuk pemberitahuan maintenance sistem',
                'type' => 'all',
                'category' => 'system',
                'target_audience' => 'all_users',
                'is_active' => true,
                'is_auto_send' => false
            ],
            
            // Appointment Templates
            [
                'template_code' => 'APPOINTMENT_REMINDER',
                'name' => 'Pengingat Janji Dokter',
                'subject' => '📅 Pengingat Janji Dokter {lansia_name}',
                'content' => "Pengingat Janji Dokter\n\nLansia: {lansia_name}\nDokter: {doctor_name}\nSpesialis: {specialist}\nTanggal: {appointment_date}\nWaktu: {appointment_time}\nLokasi: {location}\n\nHarap datang 15 menit sebelum janji temu.",
                'variables' => json_encode(['lansia_name', 'doctor_name', 'specialist', 'appointment_date', 'appointment_time', 'location']),
                'description' => 'Template untuk pengingat janji dokter',
                'type' => 'all',
                'category' => 'appointment',
                'target_audience' => 'family_members',
                'is_active' => true,
                'is_auto_send' => true
            ]
        ];

        foreach ($templates as $template) {
            NotificationTemplate::create($template);
        }
    }
}