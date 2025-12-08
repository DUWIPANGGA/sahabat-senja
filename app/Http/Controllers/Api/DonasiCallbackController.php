<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use Illuminate\Http\Request;

class DonasiCallbackController extends Controller
{
    /**
     * Handle success callback from Midtrans
     */
    public function success(Request $request)
    {
        try {
            $orderId = $request->query('order_id');
            $transactionId = $request->query('transaction_id');
            
            $donasi = Donasi::where('kode_donasi', $orderId)->first();
            
            if (!$donasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Donasi tidak ditemukan'
                ], 404);
            }
            
            // Update status ke sukses
            $donasi->update([
                'status' => 'sukses',
                'transaction_id' => $transactionId,
                'metode_pembayaran' => 'midtrans'
            ]);
            
            // Update dana terkumpul
            if ($donasi->kampanye) {
                $donasi->kampanye->increment('dana_terkumpul', $donasi->jumlah);
                $donasi->kampanye->increment('jumlah_donatur');
            }
            
            \Log::info('Callback Success', [
                'order_id' => $orderId,
                'donasi_id' => $donasi->id,
                'status' => 'sukses'
            ]);
            
            // Return HTML untuk redirect ke app
            return response()->view('callback.success', [
                'order_id' => $orderId,
                'status' => 'success'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Callback Success Error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->view('callback.error', [
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle pending callback
     */
    public function pending(Request $request)
    {
        $orderId = $request->query('order_id');
        
        $donasi = Donasi::where('kode_donasi', $orderId)->first();
        if ($donasi) {
            $donasi->update(['status' => 'pending']);
        }
        
        return response()->view('callback.pending', [
            'order_id' => $orderId,
            'status' => 'pending'
        ]);
    }
    
    /**
     * Handle error callback
     */
    public function error(Request $request)
    {
        $orderId = $request->query('order_id');
        
        $donasi = Donasi::where('kode_donasi', $orderId)->first();
        if ($donasi) {
            $donasi->update(['status' => 'failed']);
        }
        
        return response()->view('callback.error', [
            'order_id' => $orderId,
            'status' => 'error'
        ]);
    }
    
    /**
     * Handle closed callback
     */
    public function closed(Request $request)
    {
        $orderId = $request->query('order_id');
        
        return response()->view('callback.closed', [
            'order_id' => $orderId,
            'status' => 'closed'
        ]);
    }
}