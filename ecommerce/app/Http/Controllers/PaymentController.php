<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create bKash payment.
     */
    public function bkashCreate(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        
        $result = $this->paymentService->createBkashPayment($order);
        
        return response()->json($result);
    }

    /**
     * Execute bKash payment.
     */
    public function bkashExecute(Request $request)
    {
        $result = $this->paymentService->executeBkashPayment($request->paymentID);
        
        if ($result['success']) {
            $order = Order::where('order_number', $result['order_number'])->first();
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $result['transaction_id'],
            ]);
        }
        
        return response()->json($result);
    }

    /**
     * bKash callback.
     */
    public function bkashCallback(Request $request)
    {
        if ($request->status === 'success') {
            $order = Order::where('order_number', $request->order_number)->first();
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $request->transaction_id,
            ]);
            
            return redirect()->route('checkout.success', $order->id);
        }
        
        return redirect()->route('checkout.cancel');
    }

    /**
     * Create SSLCommerz payment.
     */
    public function sslcommerzCreate(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        
        $result = $this->paymentService->createSslcommerzPayment($order);
        
        return response()->json($result);
    }

    /**
     * SSLCommerz success callback.
     */
    public function sslcommerzSuccess(Request $request)
    {
        $order = Order::where('order_number', $request->tran_id)->first();
        
        if ($order && $request->status === 'VALID') {
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $request->bank_tran_id,
            ]);
            
            return redirect()->route('checkout.success', $order->id);
        }
        
        return redirect()->route('checkout.cancel');
    }

    /**
     * SSLCommerz fail callback.
     */
    public function sslcommerzFail(Request $request)
    {
        return redirect()->route('checkout.cancel');
    }

    /**
     * SSLCommerz cancel callback.
     */
    public function sslcommerzCancel(Request $request)
    {
        return redirect()->route('checkout.cancel');
    }

    /**
     * SSLCommerz IPN handler.
     */
    public function sslcommerzIpn(Request $request)
    {
        $order = Order::where('order_number', $request->tran_id)->first();
        
        if ($order && $request->status === 'VALID') {
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $request->bank_tran_id,
            ]);
        }
        
        return response('OK');
    }
}
